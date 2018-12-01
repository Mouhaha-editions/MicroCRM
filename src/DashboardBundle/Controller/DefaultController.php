<?php

namespace DashboardBundle\Controller;

use DateTime;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\VarDumper\VarDumper;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $salesDocumentDetails = $this->getDoctrine()->getRepository('BillingBundle:SalesDocumentDetail')
            ->createQueryBuilder('sd')
            ->orderBy('sd.date', 'ASC')
            ->where('sd.date BETWEEN :today_start AND :today_stop')
            ->setParameter('today_start', (new DateTime())->setTime(0, 0, 0, 0))
            ->setParameter('today_stop', (new DateTime())->setTime(23, 59, 59))
            ->getQuery()->getResult();

        $salesDocumentDetails_demain = $this->getDoctrine()->getRepository('BillingBundle:SalesDocumentDetail')
            ->createQueryBuilder('sd')
            ->orderBy('sd.date', 'ASC')
            ->where('sd.date BETWEEN :today_start AND :today_stop')
            ->setParameter('today_start', (new DateTime())->modify('+1 days')->setTime(0, 0, 0, 0))
            ->setParameter('today_stop', (new DateTime())->modify('+1 days')->setTime(23, 59, 59))
            ->getQuery()->getResult();
        $salesDocumentDetails_next = $this->getDoctrine()->getRepository('BillingBundle:SalesDocumentDetail')
            ->createQueryBuilder('sd')
            ->orderBy('sd.date', 'ASC')
            ->where('sd.date > :today_stop')
            ->setParameter('today_stop', (new DateTime())->modify('+1 days')->setTime(23, 59, 59))
            ->getQuery()->getResult();


        $start1 = DateTime::createFromFormat('Y-m-d', (date('Y') - 1) . '-01-01')->setTime(0, 0, 0, 0);
        $end1 = DateTime::createFromFormat('Y-m-d', (date('Y') - 1) . '-12-31')->setTime(23, 59, 59);

        $start2 = DateTime::createFromFormat('Y-m-d', (date('Y')) . '-01-01')->setTime(0, 0, 0, 0);
        $end2 = DateTime::createFromFormat('Y-m-d', (date('Y')) . '-12-31')->setTime(23, 59, 59);

        $ca = $this->getDoctrine()->getRepository('BillingBundle:SalesDocumentDetail')
            ->createQueryBuilder('sdd')
            ->leftJoin('sdd.salesDocument', 'sd')
            ->select('SUM(sdd.total_amount_ttc * CASE WHEN (sd.state = 200) THEN 1 ELSE (CASE WHEN (sd.state = 300) THEN  -1 ELSE 0 END) END)')
            ->where("sd.date BETWEEN :start AND :end")
            ->setParameter('start', $start1)
            ->setParameter('end', $end1)
            ->getQuery()->getOneOrNullResult();

        $ca2 = $this->getDoctrine()->getRepository('BillingBundle:SalesDocumentDetail')
            ->createQueryBuilder('sdd')
            ->leftJoin('sdd.salesDocument', 'sd')
            ->select('SUM(sdd.total_amount_ttc * CASE WHEN (sd.state = 200) THEN 1 ELSE (CASE WHEN (sd.state = 300) THEN  -1 ELSE 0 END) END)')
            ->where("sd.date BETWEEN :start AND :end")
            ->setParameter('start', $start2)
            ->setParameter('end', $end2)
            ->getQuery()->getOneOrNullResult();

        $ca_last_year = array_pop($ca);
        $ca_this_year = array_pop($ca2);

        return $this->render('@Dashboard/Default/index.html.twig', [
            'rdv' => $salesDocumentDetails,
            'rdv_demain' => $salesDocumentDetails_demain,
            'rdv_next' => $salesDocumentDetails_next,
            'ca_last_year' => $ca_last_year,
            'ca_this_year' => $ca_this_year,
        ]);
    }
}
