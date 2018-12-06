<?php

namespace BankBundle\Controller;

use BankBundle\Entity\Account;
use BankBundle\Entity\Operation;
use BankBundle\Entity\OperationCategory;
use BankBundle\Entity\OperationTiers;
use BankBundle\Form\AccountType;
use BankBundle\Form\OperationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Account controller.
 *
 */
class OperationController extends Controller
{
    /**
     * Lists all account entities.
     * @param Request $request
     * @param Account $account
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, Account $account)
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository('BankBundle:Operation')->createQueryBuilder('o')
            ->where('o.account = :account')
            ->setParameter('account', $account);

        $pagination = $this->get('pkshetlie.pagination')->process($qb, $request);
        return $this->render('@Bank/Operation/index.html.twig', array(
            'account' => $account,
            'operations' => $pagination,
        ));
    }

    /**
     * Creates a new account entity.
     * @param Request $request
     * @param Account $account
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function newAction(Request $request, Account $account)
    {
        $operation = new Operation();
        $operation->setDate(new \DateTime());
        $operation->setAccount($account);
        return $this->editAction($request, $operation);
    }

    /**
     * Displays a form to edit an existing account entity.
     * @param Request $request
     * @param Operation $operation
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Operation $operation)
    {
        $form = $this->createForm(OperationType::class, $operation);
        $form->get('category')->setData($operation->getCategory()->getId());
        $form->get('category_text')->setData($operation->getCategory()->getLabel());
        $form->get('tiers')->setData($operation->getCategory()->getId());
        $form->get('tiers_text')->setData($operation->getCategory()->getLabel());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $id_category = $form->get('category')->getData();
            $category = $form->get('category_text')->getData();
            $id_tiers = $form->get('tiers')->getData();
            $tiers = $form->get('tiers_text')->getData();

            if ($id_category != null) {
                $category_entity = $this->getDoctrine()->getRepository('BankBundle:OperationCategory')->find($id_category);
            } else {
                $category_entity = $this->getDoctrine()->getRepository('BankBundle:OperationCategory')->findOneBy(['label' => $category]);
                if ($category_entity === null) {
                    $category_entity = new OperationCategory();
                    $category_entity->setLabel(ucfirst(strtolower($category)));
                    $this->getDoctrine()->getManager()->persist($category_entity);
                }
            }
            $operation->setCategory($category_entity);
            $category_entity->addOperation($operation);

            if ($id_tiers != null) {
                $tiers_entity = $this->getDoctrine()->getRepository('BankBundle:OperationTiers')->find($id_tiers);
            } else {

                $tiers_entity = $this->getDoctrine()->getRepository('BankBundle:OperationTiers')->findOneBy(['label' => $tiers]);
                if ($tiers_entity === null) {
                    $tiers_entity = new OperationTiers();
                    $tiers_entity->setLabel(ucfirst(strtolower($tiers)));
                    $this->getDoctrine()->getManager()->persist($tiers_entity);
                }
            }
            $operation->setTiers($tiers_entity);
            $tiers_entity->addOperation($operation);


            $this->getDoctrine()->getManager()->persist($operation);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Operation enregistrée");

            return $this->redirectToRoute('bank_operation_index', ['id' => $operation->getAccount()->getId()]);
        }

        return $this->render('@Bank/Operation/edit.html.twig', array(
            'account' => $operation,
            'form' => $form->createView(),
        ));
    }

    public function deleteAction(Request $request, Operation $operation)
    {
        $account_id = $operation->getAccount()->getId();
        try {
            $this->getDoctrine()->getManager()->remove($operation);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "Operation supprimée");
        } catch (\Exception $e) {
            $this->addFlash('danger', "Erreur");

        }

        return $this->redirectToRoute('bank_operation_index', ['id' => $account_id]);
    }

}