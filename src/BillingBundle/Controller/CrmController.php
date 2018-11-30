<?php

namespace BillingBundle\Controller;


use BillingBundle\Entity\SalesDocument;
use BillingBundle\Entity\SalesDocumentDetail;
use BillingBundle\Entity\SalesDocumentProduct;
use BillingBundle\Exception\BillingDateException;
use BillingBundle\Form\SalesDocumentProductType;
use BillingBundle\Form\SalesDocumentType;
use CustomerBundle\Entity\Customer;
use CustomerBundle\Entity\CustomerAddress;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;
use Pkshetlie\SettingsBundle\Controller\ControllerWithSettings;
use Pkshetlie\SettingsBundle\Entity\Setting;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class CrmController extends ControllerWithSettings
{

    public function indexAction(Request $request)
    {
        $form = $this->createFormBuilder(null, ['method' => 'get']);
        $form->add('search', TextType::class, ['required' => false]);
        $form->add('Ok', SubmitType::class);
        $form = $form->getForm();
        $form->handleRequest($request);

        $qb = $this->getDoctrine()->getRepository('BillingBundle:SalesDocument')->createQueryBuilder('sd')
            ->orderBy('sd.state','ASC')
            ->addOrderBy('sd.chrono','DESC');


        if ($form->get('search')->getNormData()) {
            $qb->leftJoin('sd.customer', 'c');
            $qb->leftJoin('c.customer_addresses', 'a');
            $qb->leftJoin('c.customer_communications', 'cc');
            $qb->where('c.companyName LIKE :search')
                ->orWhere('sd.chrono LIKE :search')
                ->orWhere('c.lastName LIKE :search')
                ->orWhere('c.firstName LIKE :search')
                ->orWhere('c.comment LIKE :search')
                ->orWhere('cc.value LIKE :search')
                ->orWhere('a.ville LIKE :search')
                ->orWhere('a.codePostal LIKE :search')
                ->setParameter('search', $form->get('search')->getNormData() . '%');
            $qb->groupBy('sd.id');
        }


        $factures = $this->get('pkshetlie.pagination')->process($qb, $request);

        return $this->render('@Billing/Crm/sales_document_list.html.twig', [
            'factures' => $factures,
            'search_form' => $form->createView()
        ]);

    }

    public function newAction(Request $request)
    {
        $salesDocument = new SalesDocument();
        $salesDocument->addDetail(new SalesDocumentDetail());

        return $this->editAction($request, $salesDocument);
    }

    public function productCreateEditAction(Request $request, SalesDocumentProduct $product = null)
    {
        $em = $this->getDoctrine()->getManager();
        if ($product == null) {
            $product = new SalesDocumentProduct();
            $product->setTaxes(20);
        }

        $form = $this->createForm(SalesDocumentProductType::class, $product);
        $form->add('Enregistrer', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Produit ajouté');
            return $this->redirectToRoute('crm_billing_product_index');
        }

        return $this->render('@Billing/Crm/form_product.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function productsAction(Request $request)
    {
        /** @var SalesDocumentProduct $products */
        $products = $this->getDoctrine()->getRepository('BillingBundle:SalesDocumentProduct')
            ->createQueryBuilder('p')
            ->orderBy('p.label', 'ASC')
            ->getQuery()->getResult();
        return $this->render('@Billing/Crm/index_product.html.twig', [
            'products' => $products
        ]);
    }

    public function editAction(Request $request, SalesDocument $salesDocument)
    {
        $originalDetails = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($salesDocument->getDetails() as $detail) {
            $originalDetails->add($detail);
        }
        $salesDocument->setDate(new DateTime());

        $form = $this->createForm(SalesDocumentType::class, $salesDocument);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                foreach ($originalDetails as $detail) {
                    if (false === $salesDocument->getDetails()->contains($detail)) {
                        $salesDocument->getDetails()->removeElement($detail);
                        $em->remove($detail);
                    }
                }

                foreach ($salesDocument->getDetails() AS $det) {
                    $det->setSalesDocument($salesDocument);
                }


                try {
                    $this->get('facturation.service')->process($salesDocument);
                    $em->persist($salesDocument);
                    $em->flush();
                    $this->addFlash('success', 'Document enregistré');
                    return $this->redirectToRoute('crm_billing_salesdocument_index');
                }catch(BillingDateException $e){
                    $form->get('date')->addError(new FormError($e->getMessage()));
                    $this->addFlash('danger', $e->getMessage());
                } catch (OptimisticLockException $e) {
                    $this->addFlash('danger', $e->getMessage());

                } catch (ORMException $e) {
                    $this->addFlash('danger', $e->getMessage());
                }

            } else {
                $this->addFlash('danger', "Erreur lors de la saisie");
            }
        }

        $qb = $this->getDoctrine()->getRepository('BillingBundle:SalesDocumentProduct')
            ->createQueryBuilder('p')
            ->orderBy('p.label');
        $products = $qb->getQuery()->getResult();

        return $this->render('@Billing/Crm/sales_document_form.html.twig', [
            'form' => $form->createView(),
            'products' => $products,
        ]);
    }

    public function documentAction(Request $request, SalesDocument $salesDocument = null)
    {
        try {
            $salesDocument = $this->getDoctrine()->getRepository('BillingBundle:SalesDocument')->find($request->get('id'));
            $dir = $this->getParameter('kernel.project_dir');
            $template = $this->getSetting('facture_template_name');
            $templateProcessor = new TemplateProcessor($dir . '/src/BillingBundle/Resources/word/' . $template . '.docx');
            /** @var Customer $customer */
            $customer = $salesDocument->getCustomer();
            $templateProcessor->setValue('Client.Name', $customer->getFullName());
            /** @var CustomerAddress $address */
            $address = $customer->getCustomerAddresses()->get(0);
            $templateProcessor->setValue('Client.Address1', $address->getLigne1());
            $templateProcessor->setValue('Client.Address2', $address->getLigne2());
            $templateProcessor->setValue('Client.PostCode', $address->getCodePostal());
            $templateProcessor->setValue('Client.City', $address->getVille());
            $templateProcessor->setValue('Client.Siret', $customer->getSiret());
            $templateProcessor->setValue('Facture.Reference', $salesDocument->getChrono());
            $templateProcessor->setValue('Facture.Date', $salesDocument->getDateStr());
            $templateProcessor->setValue('Facture.TotalTtc', number_format($salesDocument->getTotalTTC(), 2, ',', ' '));
            $templateProcessor->setValue('Facture.TotalHt', number_format($salesDocument->getTotalHT(), 2, ',', ' '));

            /**
             * @var int $i
             * @var SalesDocumentDetail $detail
             */
            $templateProcessor->cloneRow('Facture.Detail.Label', $salesDocument->getDetails()->count());
            foreach ($salesDocument->getDetails() AS $i => $detail) {
                $templateProcessor->setValue('Facture.Detail.Label#' . ($i + 1), $detail->getLabel());
                $templateProcessor->setValue('Facture.Detail.Description#' . ($i + 1), $detail->getDescription());
                $templateProcessor->setValue('Facture.Detail.Quantity#' . ($i + 1), $detail->getQuantity());
                $templateProcessor->setValue('Facture.Detail.UnitPriceHt#' . ($i + 1), number_format($detail->getUnitAmountHt(), 2, ',', ' '));
                $templateProcessor->setValue('Facture.Detail.TotalPriceHt#' . ($i + 1), number_format($detail->getTotalAmountHt(), 2, ',', ' '));
                $templateProcessor->setValue('Facture.Detail.TotalPriceTTC#' . ($i + 1), number_format($detail->getTotalAmountTtc(), 2, ',', ' '));
            }
            $file = $dir . '/src/BillingBundle/Resources/factures/' . $salesDocument->getChrono() . '.docx';
            $templateProcessor->saveAs($file);

        } catch (CopyFileException $e) {
        } catch (CreateTemporaryFileException $e) {
        }
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $salesDocument->getChrono() . '.docx'
        );

        return $response;
    }
}

