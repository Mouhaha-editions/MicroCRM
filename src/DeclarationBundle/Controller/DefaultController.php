<?php

namespace DeclarationBundle\Controller;

use BillingBundle\Entity\SalesDocument;
use Pkshetlie\SettingsBundle\Controller\ControllerWithSettings;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends ControllerWithSettings
{
    public function indexAction(Request $request)
    {
        $factures = [];
        $taxes = [];

        $start = new \DateTime($this->getSetting('declaration_start'));
        while ($start->format('Y') < date('Y') + 1) {
            $date_start = clone $start;
            $start->modify($this->getSetting('declaration_next'));
            $date_end = clone $start;
            $factures[$date_start->format('d/m/Y').'-'.$date_end->format('d/m/Y')] = $this->getDoctrine()->getRepository('BillingBundle:SalesDocument')
                ->createQueryBuilder('sd')
                ->select('SUM(d.total_amount_ttc) AS sum, d.taxes_to_apply AS taxe')
                ->leftJoin('sd.details','d')
                ->andWhere('(sd.paymentDate BETWEEN :start AND :end AND  sd.state = :facture ) OR (sd.date BETWEEN :start AND :end AND  sd.state = :bon_commande )')
                ->setParameter('start', $date_start)
                ->setParameter('end', $date_end)
                ->setParameter('facture', SalesDocument::FACTURE)
                ->setParameter('bon_commande', SalesDocument::BON_COMMANDE)
                ->groupBy('d.taxes_to_apply')
            ->getQuery()->getArrayResult();

            $taxes[$date_start->format('d/m/Y').'-'.$date_end->format('d/m/Y')] = $this->getDoctrine()->getRepository('BillingBundle:SalesDocument')
                ->createQueryBuilder('sd')
                ->select('SUM((d.total_amount_ttc* d.taxes_to_apply / 100)) AS sum, d.taxes_to_apply AS taxe')
                ->leftJoin('sd.details','d')
                ->andWhere('(sd.paymentDate BETWEEN :start AND :end AND  sd.state = :facture ) OR (sd.date BETWEEN :start AND :end AND  sd.state = :bon_commande )')
                ->groupBy('d.taxes_to_apply')
                ->setParameter('start', $date_start)
                ->setParameter('end', $date_end)
                ->setParameter('facture', SalesDocument::FACTURE)
                ->setParameter('bon_commande', SalesDocument::BON_COMMANDE)
//                ->groupBy('1')
                ->getQuery()->getArrayResult();
        }

        return $this->render('@Declaration/Default/index.html.twig',[
            'factures'=>$factures,
            'taxes'=>$taxes,
        ]);
    }
}
