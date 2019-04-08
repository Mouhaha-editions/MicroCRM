<?php

namespace BillingBundle\Service;


use BillingBundle\Exception\BillingDateException;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use BillingBundle\Entity\SalesDocument;
use BillingBundle\Entity\SalesDocumentDetail;
use Pkshetlie\SettingsBundle\Services\SettingsService;

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

    /**
     * @param SalesDocument $sd
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws BillingDateException
     */
    public function process(SalesDocument $sd)
    {
        if ($sd->isFacture()) {
            $sd->setDate($this->getDate($sd));
            $sd->setChrono($this->getChrono($sd));
        }
        if ($sd->isAvoir()) {
            $sd->setDate($this->getDate($sd, SalesDocument::AVOIR));
            $sd->setChrono($this->getChrono($sd, SalesDocument::AVOIR));
        }
        if ($sd->isDevis()) {
            $sd->setDate($this->getDate($sd, SalesDocument::DEVIS));
            $sd->setChrono($this->getChrono($sd, SalesDocument::DEVIS));
        }
        $this->checkIfPaid($sd);
        $this->_em->flush();
    }

    /**
     * @param SalesDocument $sd
     * @param int $type
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function getChrono(SalesDocument $sd, $type = SalesDocument::FACTURE)
    {
        if ($type == SalesDocument::FACTURE) {
            $template = $this->setting_service->get('facture_numerotation_template');
            $last_bill = $this->setting_service->get('facture_numerotation');
            $this->setting_service->set('facture_numerotation', $last_bill + 1);
        } else if ($type == SalesDocument::AVOIR) {
            $template = $this->setting_service->get('avoir_numerotation_template');
            $last_bill = $this->setting_service->get('avoir_numerotation');
            $this->setting_service->set('avoir_numerotation', $last_bill + 1);
        }else if ($type == SalesDocument::DEVIS) {
            $template = $this->setting_service->get('devis_numerotation_template');
            $last_bill = $this->setting_service->get('devis_numerotation');
            $this->setting_service->set('devis_numerotation', $last_bill + 1);
        }
        $template = str_replace('{YEAR2}', $sd->getDate()->format('y'), $template);
        $template = str_replace('{YEAR}', $sd->getDate()->format('Y'), $template);
        $template = str_replace('{YEAR4}', $sd->getDate()->format('Y'), $template);
        $template = str_replace('{MONTH2}', $sd->getDate()->format('m'), $template);
        $template = str_replace('{AUTO}', str_pad($last_bill + 1, 4, '0', STR_PAD_LEFT), $template);
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


    /**
     * @param SalesDocument $sd
     * @param int $type
     * @return DateTime
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws BillingDateException
     * @throws Exception
     */
    private function getDate(SalesDocument $sd, $type = SalesDocument::FACTURE)
    {
        if ($sd->getDate() == null) {
            $this->setting_service->set('facture_numerotation_date', date('Y-m-d'));
            return new DateTime();
        }
        if ($type == SalesDocument::FACTURE) {
            $date = DateTime::createFromFormat('Y-m-d', $this->setting_service->get('facture_numerotation_date'));
            if ($date->format('Y-m-d') > $sd->getDate()->format('Y-m-d')) {
                throw new BillingDateException('La date de la nouvelle facture ne peut pas être enterieure a la date de la facture la plus récente');
            }

        } else if ($type == SalesDocument::AVOIR) {
            $date = DateTime::createFromFormat('Y-m-d', $this->setting_service->get('avoir_numerotation_date'));
            if ($date->format('Y-m-d') > $sd->getDate()->format('Y-m-d')) {
                throw new BillingDateException('La date du nouvel avoir ne peut pas être enterieure a la date de l\'avoir le plus récent');
            }
        } else if ($type == SalesDocument::DEVIS) {
            $date = DateTime::createFromFormat('Y-m-d', $this->setting_service->get('devis_numerotation_date'));
            if ($date->format('Y-m-d') > $sd->getDate()->format('Y-m-d')) {
                throw new BillingDateException('La date du nouveau devis ne peut pas être enterieure à la date du devis le plus récent');
            }
        }
        $this->setting_service->set('facture_numerotation_date', $sd->getDate()->format('Y-m-d'));

        return $sd->getDate();
    }

    private function checkIfPaid(SalesDocument $sd)
    {
        if ($sd->getIsPaid()) {
            $this->applyAccountAndFidelity($sd);
        }
    }

    public function toogleIsPaid(SalesDocument $sd)
    {
        $sd->setIsPaid(!$sd->getIsPaid());
        $this->applyAccountAndFidelity($sd);
        $this->_em->flush();
    }

    private function applyAccountAndFidelity(SalesDocument $sd)
    {
        $ratio = $this->setting_service->get('ratio_points_fidelite', 0);
        $account = $this->setting_service->get('mon_compte', 0);

        if ($sd->getState() == SalesDocument::FACTURE) {
            $sd->getCustomer()->setPointsFidelite($sd->getCustomer()->getPointsFidelite() + ($ratio * $sd->getTotalTTC() *($sd->getIsPaid() ? 1:-1)));
            $this->setting_service->set('mon_compte', $account + (int)($sd->getTotalTTC()*100*($sd->getIsPaid() ? 1:-1)));
        }
        if ($sd->getState() == SalesDocument::AVOIR) {
            $sd->getCustomer()->setPointsFidelite($sd->getCustomer()->getPointsFidelite() - ($ratio * $sd->getTotalTTC()*($sd->getIsPaid() ? 1:-1)));
            $this->setting_service->set('mon_compte', $account - (int)($sd->getTotalTTC()*100*($sd->getIsPaid() ? 1:-1)));
        }
    }
}