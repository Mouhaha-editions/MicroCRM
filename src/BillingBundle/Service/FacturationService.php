<?php

namespace BillingBundle\Service;


use BillingBundle\Exception\BillingDateException;
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
use Symfony\Component\Validator\Constraints\Date;
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

        $this->_em->flush();
    }

    /**
     * @param int $type
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function getChrono(SalesDocument $sd,$type = SalesDocument::FACTURE)
    {

        if ($type == SalesDocument::FACTURE) {
            $template = $this->setting_service->get('facture_numerotation_template');
            $last_bill = $this->setting_service->get('facture_numerotation');
            $this->setting_service->set('facture_numerotation', $last_bill + 1);
        } else {
            $template = $this->setting_service->get('avoir_numerotation_template');
            $last_bill = $this->setting_service->get('avoir_numerotation');
            $this->setting_service->set('avoir_numerotation', $last_bill + 1);
        }
        $template = str_replace('{YEAR2}',$sd->getDate()->format('y'), $template);
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
        if($sd->getDate() == null){
            $this->setting_service->set('facture_numerotation_date', date('Y-m-d'));
            return new DateTime();
        }
        if ($type == SalesDocument::FACTURE) {
            $date = DateTime::createFromFormat('Y-m-d',$this->setting_service->get('facture_numerotation_date'));
            if($date->format('Y-m-d') > $sd->getDate()->format('Y-m-d')){
                throw new BillingDateException('La date de la nouvelle facture ne peut pas être enterieure a la date de la facture la plus récente');
            }

        } else {
            $date = DateTime::createFromFormat('Y-m-d',$this->setting_service->get('avoir_numerotation_date'));
            if($date->format('Y-m-d') > $sd->getDate()->format('Y-m-d')){
                throw new BillingDateException('La date du nouvel avoir ne peut pas être enterieure a la date de l\'avoir le plus récent');
            }
        }
        $this->setting_service->set('facture_numerotation_date', $sd->getDate()->format('Y-m-d'));

        return $sd->getDate();
    }
}