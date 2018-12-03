<?php

namespace CustomerBundle\Controller;

use BillingBundle\Entity\SalesDocument;
use BillingBundle\Entity\SalesDocumentDetail;
use CustomerBundle\Entity\Customer;
use CustomerBundle\Entity\CustomerAddress;
use CustomerBundle\Entity\CustomerCommunication;
use CustomerBundle\Enums\ECustomerCommunicationType;
use CustomerBundle\Enums\ECustomerType;
use CustomerBundle\Form\CustomerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Customer controller.
 *
 */
class CustomerController extends Controller
{
    /**
     * Lists all customer entities.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder(null, ['method' => 'get']);
        $form->add('search', TextType::class, ['required' => false]);
        $form->add('Ok', SubmitType::class);
        $form = $form->getForm();
        $form->handleRequest($request);
        $qb = $em->getRepository('CustomerBundle:Customer')->createQueryBuilder('c');

        if ($form->get('search')->getNormData()) {
            $qb->leftJoin('c.customer_addresses', 'a');
            $qb->leftJoin('c.customer_communications', 'cc');
            $qb->where('c.companyName LIKE :search')
                ->orWhere('c.lastName LIKE :search')
                ->orWhere('c.firstName LIKE :search')
                ->orWhere('c.comment LIKE :search')
                ->orWhere('cc.value LIKE :search')
                ->orWhere('a.ville LIKE :search')
                ->orWhere('a.codePostal LIKE :search')
                ->setParameter('search', $form->get('search')->getNormData() . '%');

            $qb->groupBy('c.id');
        }
        $pagination = $this->get('pkshetlie.pagination')->process($qb, $request);

        return $this->render('@Customer/customer/index.html.twig', array(
            'customers' => $pagination,
            'search_form' => $form->createView(),
        ));
    }

    /**
     * Creates a new customer entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $customer = new Customer();
        $customer->setCustomerType(ECustomerType::CLIENT);
        $communication = new CustomerCommunication();
        $communication->setCustomerCommunicationType(ECustomerCommunicationType::EMAIL);

        $communication2 = new CustomerCommunication();
        $communication2->setCustomerCommunicationType(ECustomerCommunicationType::TELEPHONE);

        $communication3 = new CustomerCommunication();
        $communication3->setCustomerCommunicationType(ECustomerCommunicationType::MOBILE);

        $customer->addCustomerCommunication($communication);
        $customer->addCustomerCommunication($communication2);
        $customer->addCustomerCommunication($communication3);

        $customer->addCustomerAddress(new CustomerAddress());
        return $this->editAction($request, $customer);
    }

    /**
     * Finds and displays a customer entity.
     *
     */
    public function showAction(Customer $customer)
    {
        $deleteForm = $this->createDeleteForm($customer);

        return $this->render('@Customer/customer/show.html.twig', array(
            'customer' => $customer,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing customer entity.
     *
     */
    public function editAction(Request $request, Customer $customer)
    {
        $editForm = $this->createForm(CustomerType::class, $customer);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            /** @var CustomerCommunication $com */
            foreach ($customer->getCustomerCommunications() AS $com) {
                if ($com->getCustomerCommunicationType() == null && $com->getValue() == null) {
                    $customer->removeCustomerCommunication($com);
                }
            }

            $this->getDoctrine()->getManager()->persist($customer);
            $this->getDoctrine()->getManager()->flush();
            $t = $this->get('translator');
            $this->addFlash('success', $t->trans('customer_bundle.success_edit_add'));
            return $this->redirectToRoute('customer_index');
        }
        /** @var SalesDocument[] $details */
        $details = [];
        if ($customer->getId() !== null) {
            $details = $this->getDoctrine()->getRepository('BillingBundle:SalesDocument')
                ->createQueryBuilder('sd')
                ->leftJoin('sd.details', 'sdd')
                ->orderBy('sd.id', 'desc')
                ->where('sd.state IN (:states)')
                ->andWhere('sd.customer = :customer')
                ->setParameter('states', [SalesDocument::FACTURE, SalesDocument::BON_COMMANDE])
                ->setParameter('customer', $customer)
                ->setMaxResults(40)
                ->setFirstResult(0)
                ->getQuery()->getResult();
        }

        return $this->render('@Customer/customer/edit.html.twig', array(
            'customer' => $customer,
            'form' => $editForm->createView(),
            'salesDocuments' => $details
        ));
    }

    public function exportAction()
    {
        /** @var Customer[] $clients */
        $clients = $this->getDoctrine()->getRepository('CustomerBundle:Customer')
            ->createQueryBuilder('c')
            ->select(('DISTINCT c'))
            ->leftJoin('c.customer_communications', 'cc')
            ->leftJoin('c.customer_addresses', 'ca')
            ->getQuery()->getResult();
        $fileName = "export_clients_" . date("d_m_Y") . ".csv";
        $response = new StreamedResponse();


        $response->setCallback(function () use ($clients) {
            $handle = fopen('php://output', 'w+');

            // Nom des colonnes du CSV
            fputcsv($handle, array(
                'Nom',
                utf8_decode('Prénom'),
                'Date de naissance',
                utf8_decode('Points fidelité'),
                'Email',
                'Mobile',
                utf8_decode('Téléphone'),
                'Adresse 1',
                'Adresse 2',
                'Code Postal',
                'Ville',
            ), ';');

            //Champs
            foreach ($clients as $index => $client) {
                $address = $client->getAddresse();
                //dump($client);die();
                fputcsv($handle, array(
                    utf8_decode($client->getLastName() ?: $client->getCompanyName()),
                    utf8_decode($client->getFirstName()),
                    $client->getBirthday()->format('d/m/Y'),
                    $client->getPointsFidelite(),
                    utf8_decode($client->getEmail()->getValue()),
                    $client->getMobile()->getValue(),
                    $client->getTelephone()->getValue(),
                    utf8_decode($address->getLigne1()),
                    utf8_decode($address->getLigne2()),
                    $address->getCodePostal(),
                    utf8_decode($address->getVille()),
                ), ';');
            }
            fclose($handle);
        });


        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8', 'application/force-download');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $fileName);


        return $response;
    }

    /**
     * Deletes a customer entity.
     *
     */
    public function deleteAction(Request $request, Customer $customer)
    {
        $form = $this->createDeleteForm($customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($customer);
            $em->flush();
        }

        return $this->redirectToRoute('customer_index');
    }

}
