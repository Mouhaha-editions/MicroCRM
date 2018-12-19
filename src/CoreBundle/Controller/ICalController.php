<?php

namespace CoreBundle\Controller;

use BillingBundle\Entity\SalesDocumentDetail;
use Pkshetlie\SettingsBundle\Controller\ControllerWithSettings;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ICalController extends ControllerWithSettings
{
    public function indexAction(Request $request)
    {
        if($this->getSetting('ical_key', false) && $request->get('key') !== $this->getSetting('ical_key', false)){
            return new Response('',401);
        }
        $provider = $this->get('bomo_ical.ics_provider');

        $tz = $provider->createTimezone();
        $tz
            ->setTzid('Europe/Paris')
            ->setProperty('X-LIC-LOCATION', $tz->getTzid());

        $cal = $provider->createCalendar($tz);

        $cal
            ->setName('Micro CRM')
            ->setDescription("Calendrier depuis l'application Micro CRM");
        /** @var SalesDocumentDetail $detail */
        foreach ($this->getDoctrine()->getRepository('BillingBundle:SalesDocumentDetail')
                     ->createQueryBuilder('d')
                     ->where('d.date IS NOT NULL')
                     ->getQuery()->getResult() AS $detail) {
            $event = $cal->newEvent();
            $event
                ->setStartDate($detail->getDate())
                ->setEndDate($detail->getEndDate())
                ->setName($detail->getSalesDocument()->getCustomer()->getFullName())
                ->setDescription($detail->getLabel() . "<br/>" . $detail->getDescription() . "<br/>" . $detail->getSalesDocument()->getComment());
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
