<?php

namespace BillingBundle\Controller;


use BillingBundle\Entity\Payment;
use BillingBundle\Form\PaymentType;
use DateTime;
use Pkshetlie\SettingsBundle\Controller\ControllerWithSettings;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class AccountController extends ControllerWithSettings
{

    public function indexAction(Request $request)
    {
        $form = $this->createFormBuilder(null, ['method' => 'get']);
        $form->add('start_date', DateType::class, [
            'format' => DateType::HTML5_FORMAT,
            'widget' => 'single_text',
            'required' => false
        ]);
        $form->add('end_date', DateType::class, [
            'required' => false
        ]);
        $form->add('Ok', SubmitType::class);
        $form = $form->getForm();
        $form->handleRequest($request);

        $qb = $this->getDoctrine()->getRepository('BillingBundle:Payment')
            ->createQueryBuilder('p')
            ->orderBy('p.date', 'DESC')
            ->addOrderBy('p.id', 'DESC');


        if ($form->get('start_date')->getNormData()) {

        }
        $account = $this->getSetting('mon_compte');

        $payments = $this->get('pkshetlie.pagination')->process($qb, $request);

        $sum_theo = ($this->getDoctrine()->getRepository('BillingBundle:Payment')->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->setFirstResult(0)->setMaxResults(1)
            ->getQuery()->getOneOrNullResult());;
        $sum_theo = array_pop($sum_theo);

        $sum_reel = $this->getDoctrine()->getRepository('BillingBundle:Payment')->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->where('p.date <= :now')
            ->setParameter('now', new DateTime())
            ->setFirstResult(0)->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();;
        $sum_reel = array_pop($sum_reel);

        return $this->render('@Billing/Account/index.html.twig', [
            'payments' => $payments,
            'search_form' => $form->createView(),
            'account_theo' => $account/100 + $sum_theo,
            'account_reel' => $account/100 + $sum_reel,
            'search_form' => $form->createView(),
        ]);

    }

    public function newAction(Request $request)
    {
        $payment = new Payment();
        return $this->editAction($request, $payment);
    }

    public function editAction(Request $request, Payment $payment)
    {
        $payment->setDate(new DateTime());
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($payment);
                $em->flush();
                $this->addFlash('success', 'Paiement enregistré');
                return $this->redirectToRoute('account_index');
            } else {
                $this->addFlash('danger', "Erreur lors de la saisie");
            }
        }


        return $this->render('@Billing/Account/payment_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    public function deleteAction(Request $request, Payment $payment = null)
    {
        $this->getDoctrine()->getManager()->remove($payment);
        $this->getDoctrine()->getManager()->flush($payment);
        return $this->redirectToRoute('account_index');
    }
}

