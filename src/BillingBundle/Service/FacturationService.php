<?php

namespace BillingBundle\Service;


use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use BillingBundle\Entity\SalesDocument;
use BillingBundle\Entity\SalesDocumentDetail;
use BillingBundle\Entity\SalesDocumentMensualisation;
use BillingBundle\Entity\SalesDocumentPayment;
use Pkshetlie\SettingsBundle\Services\SettingsService;
use Symfony\Component\VarDumper\VarDumper;

class FacturationService
{
    /** @var EntityManagerInterface */
    private $_em;
    private $setting_service;

    public function __construct(EntityManagerInterface $em, SettingsService $setting_service)
    {
        $this->_em = $em;
        $this->setting_service = $setting_service;
    }

    public function process(SalesDocument $sd)
    {
        if ($sd->isFacture()) {
            $sd->setChrono($this->getChrono());
            $sd->setDate(new DateTime());
        }
        if ($sd->isAvoir()) {
            $sd->setChrono($this->getChrono(SalesDocument::AVOIR));
            $sd->setDate(new DateTime());
        }

        $this->_em->flush();
    }

    /**
     * @param int $type
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function getChrono($type = SalesDocument::FACTURE)
    {
        if ($type == SalesDocument::FACTURE) {
            $template = $this->setting_service->get('facture_numerotation_template');
            $last_bill = $this->setting_service->get('facture_numerotation');
            $this->setting_service->set('facture_numerotation',$last_bill + 1);
        } else {
            $template = $this->setting_service->get('avoir_numerotation_template');
            $last_bill = $this->setting_service->get('avoir_numerotation');
            $this->setting_service->set('avoir_numerotation',$last_bill + 1);
        }
        $template = str_replace('{YEAR2}', date('y'), $template);
        $template = str_replace('{YEAR}', date('Y'), $template);
        $template = str_replace('{YEAR4}', date('Y'), $template);
        $template = str_replace('{MONTH2}', date('m'), $template);
        $template = str_replace('{AUTO}', str_pad($last_bill + 1,4,'0',STR_PAD_LEFT), $template);
        return $template;
    }

    public function cleanEmptyDetails(SalesDocument $sd)
    {
        foreach ($sd->getDetails() AS $id => $detail) {
            if (empty($detail->getLabel())) {
                $sd->removeDetail($detail);
                $detail->setSalesDocument(null);
            }
        }
    }

    public function closeFacture(SalesDocument $sd)
    {
        /**
         * @var int $id
         * @var SalesDocumentPayment $payment
         */
        foreach ($sd->getPayments() AS $id => $payment) {
            if ($payment->getState() == SalesDocumentPayment::STATE_WAITING) {
                $payment->setSalesDocument(null);
                $this->_em->remove($payment);
            }
        }
        $this->_em->flush();

        if ($sd->getRestantAPayer() != 0) {
            $p = new SalesDocumentPayment();
            $p->setDate(new DateTime());
            $p->setLabel("Cloture suite à avoir");
            $p->setAmount($sd->getRestantAPayer());
            $p->setType(SalesDocumentPayment::TYPE_CLOTURE_FACTURE);
            $p->setState(SalesDocumentPayment::STATE_PAID);
            $p->setSalesDocument($sd);
            $sd->addPayment($p);
            $this->_em->persist($p);
            $this->_em->flush();
        }
    }

    public function createEmptyDetails(SalesDocument $sd, $count = 10)
    {
        $i = $sd->getDetails()->Count() - 1;
        while ($i < $count) {
            $detail = new SalesDocumentDetail();
            $detail->setQuantity(0);
            $sd->addDetail($detail);
            $detail->setSalesDocument($sd);
            $i++;
        }
    }


    public function updatePayment(SalesDocumentPayment $payment)
    {
        /** @var SalesDocumentPayment $prev */
        $prev = $this->_em->getRepository('BillingBundle:SalesDocumentPayment')->find($payment->getId());
        if ($payment->getMensualisation() != null) {
            if ($payment->getState() == SalesDocumentPayment::STATE_UNPAID) {
                /** @var SalesDocumentPayment $next */
                $next = $this->_em->getRepository('BillingBundle:SalesDocumentPayment')->createQueryBuilder('p')
                    ->leftJoin('p.mensualisation', 'm')
                    ->where('m.id = :mensualite')
                    ->andWhere('p.date = :dateSuivante')
                    ->setParameter('mensualite', $payment->getMensualisation())
                    ->setParameter('dateSuivante', (clone $payment->getDate())->modify('+1 months'))
                    ->setFirstResult(0)->setMaxResults(1)
                    ->getQuery()->getOneOrNullResult();
                if ($next == null) {
                    $next = new SalesDocumentPayment();
                    $next->setMensualisation($payment->getMensualisation());
                    $next->setAmount($prev->getAmount());
                    $next->setLabel($prev->getLabel());
                    $next->setComment($prev->getComment());
                    $next->setState(SalesDocumentPayment::STATE_WAITING);
                    $next->setSalesDocument($prev->getSalesDocument());
                    $next->setType($prev->getType());
                    $payment->setAmount(0);
                    $next->setDate((clone $payment->getDate())->modify('+1 months'));
                    $this->_em->persist($next);
                    $this->_em->flush();

                } else {
                    $next->setAmount($next->getAmount() + $prev->getAmount());
                    $payment->setAmount(0);
                    $this->_em->flush();
                }
            }
        }
    }
}