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

        $start = new \DateTime($this->getSetting('declaration_start'));
        while ($start->format('Y') < date('Y') + 1) {
            $date_start = clone $start;
            $start->modify($this->getSetting('declaration_next'));
            $date_end = clone $start;
            $factures[$date_start->format('d/m/Y').'-'.$date_end->format('d/m/Y')] = $this->getDoctrine()->getRepository('BillingBundle:SalesDocument')
                ->createQueryBuilder('sd')
                ->select('SUM(d.total_amount_ttc) AS sum')
                ->leftJoin('sd.details','d')
                ->where('sd.state IN (:states)')
                ->andWhere('sd.paymentDate BETWEEN :start AND :end')
                ->setParameter('start', $date_start)
                ->setParameter('end', $date_end)
                ->setParameter('states', [SalesDocument::FACTURE,SalesDocument::BON_COMMANDE])
//                ->groupBy('1')
            ->getQuery()->getOneOrNullResult()['sum'];
        }

        return $this->render('@Declaration/Default/index.html.twig',[
            'factures'=>$factures
        ]);
    }
}
