<?php

namespace DashboardBundle\Controller;

use DateTime;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

        return $this->render('@Dashboard/Default/index.html.twig', [
            'rdv' => $salesDocumentDetails,
            'rdv_demain' => $salesDocumentDetails_demain,
            'rdv_next' => $salesDocumentDetails_next,
        ]);
    }
}
