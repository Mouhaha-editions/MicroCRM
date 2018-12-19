<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ICalController extends Controller
{
    public function indexAction()
    {
        $provider = $this->get('bomo_ical.ics_provider');

        $tz = $provider->createTimezone();
        $tz
            ->setTzid('Europe/Paris')
            ->setProperty('X-LIC-LOCATION', $tz->getTzid());

        $cal = $provider->createCalendar($tz);

        $cal
            ->setName('Micro CRM')
            ->setDescription("Calendrier depuis l'application Micro CRM");

        foreach ($this->getDoctrine()->getRepository('BillingBundle:SalesDocumentDetail')
                     ->createQueryBuilder('d')
                     ->where('d.date IS NOT NULL')
                     ->getQuery()->getResult() AS $detail) {
            $event = $cal->newEvent();
            $event
                ->setStartDate($detail->getDate())
                ->setEndDate($detail->getEndDate())
                ->setName($detail->getLabel())
                ->setDescription($detail->getDescription())
                ->setAttendee('pierrick.pobelle@gmail.com')
                ->setAttendee($detail->getSalesDocument()->getCustomer()->getFullName());
        }
        $calStr = $cal->returnCalendar();

        return new Response(
            $calStr,
            200,
            array(
                'Content-Type' => 'text/calendar; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="calendar.ics"',
            )
        );

    }
}
