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
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\TemplateProcessor;
use Pkshetlie\SettingsBundle\Controller\ControllerWithSettings;
use Pkshetlie\SettingsBundle\Entity\Setting;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\VarDumper\VarDumper;

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
            ->orderBy('sd.state', 'ASC')
            ->addOrderBy('sd.chrono', 'DESC');


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

    public function tooglePaidAction(Request $request, SalesDocument $sd)
    {
        $this->get('facturation.service')->toogleIsPaid($sd);
        return new JsonResponse(['success' => true]);
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
                } catch (BillingDateException $e) {
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

    public function documentAction(Request $request, SalesDocument $salesDocument = null, $pdf = false)
    {
        try {
            $salesDocument = $this->getDoctrine()->getRepository('BillingBundle:SalesDocument')->find($request->get('id'));
            $dir = $this->getParameter('kernel.project_dir');
            if($salesDocument->isFacture()) {
                $template = $this->getSetting('facture_template_name');
                $kind = "factures";
            }else if($salesDocument->isAvoir()) {
                $template = $this->getSetting('avoir_template_name');
                $kind = "avoirs";
            }else if($salesDocument->isDevis()) {
                $template = $this->getSetting('devis_template_name');
                $kind = "devis";
            }
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
            $templateProcessor->setValue('Document.Reference', $salesDocument->getChrono());
            $templateProcessor->setValue('Document.Date', $salesDocument->getDateStr());
            $templateProcessor->setValue('Document.TotalTtc', number_format($salesDocument->getTotalTTC(), 2, ',', ' '));
            $templateProcessor->setValue('Document.TotalTva', number_format($salesDocument->getTotalTTC()-$salesDocument->getTotalHT(), 2, ',', ' '));
            $templateProcessor->setValue('Document.TotalHt', number_format($salesDocument->getTotalHT(), 2, ',', ' '));

            /**
             * @var int $i
             * @var SalesDocumentDetail $detail
             */
            $templateProcessor->cloneRow('Document.Detail.Label', $salesDocument->getDetails()->count());
            $taxes = [];

            foreach ($salesDocument->getDetails() AS $i => $detail) {
                $tva = number_format($detail->getTaxes(), 2, ',', ' ');
                if (!isset($taxes[$tva])) {
                    $taxes[$tva] = $detail->getTotalAmountTtc() - $detail->getTotalAmountHt();
                } else {
                    $taxes[$tva] += $detail->getTotalAmountTtc() - $detail->getTotalAmountHt();
                }
                $templateProcessor->setValue('Document.Detail.Label#' . ($i + 1), $detail->getLabel());
                $templateProcessor->setValue('Document.Detail.Description#' . ($i + 1), $detail->getDescription());
                $templateProcessor->setValue('Document.Detail.Quantity#' . ($i + 1), $detail->getQuantity());
                $templateProcessor->setValue('Document.Detail.UnitPriceHt#' . ($i + 1), number_format($detail->getUnitAmountHt(), 2, ',', ' '));
                $templateProcessor->setValue('Document.Detail.TotalPriceHt#' . ($i + 1), number_format($detail->getTotalAmountHt(), 2, ',', ' '));
                $templateProcessor->setValue('Document.Detail.Taxe#' . ($i + 1), $tva);
                $templateProcessor->setValue('Document.Detail.TotalPriceTtc#' . ($i + 1), number_format($detail->getTotalAmountTtc(), 2, ',', ' '));
            }
            $templateProcessor->cloneRow('Taxes.Percent', count($taxes));
            $i = 0;
            ksort($taxes);
            foreach ($taxes AS $prct => $tx) {
                $templateProcessor->setValue('Taxes.Percent#' . ($i +1 ), $prct);
                $templateProcessor->setValue('Taxes.Amount#' . ($i+1 ), number_format($tx, 2, ',', ' '));
                $i++;
            }
            $file = $dir . '/src/BillingBundle/Resources/'.$kind.'/' . $salesDocument->getChrono() . '.docx';
            $templateProcessor->saveAs($file);
            if ($pdf) {
                $docxfile = $file;
                \PhpOffice\PhpWord\Settings::setPdfRendererPath($dir . '/vendor/dompdf/dompdf/src');
                \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
                $file = $dir . '/src/BillingBundle/Resources/'.$kind.'/' . $salesDocument->getChrono() . '.pdf';
                $temp = \PhpOffice\PhpWord\IOFactory::load($docxfile);
                $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($temp, 'PDF');
                $xmlWriter->save($file, TRUE);
            }
        } catch (Exception $e) {
            VarDumper::dump($e);die;
        }
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $salesDocument->getChrono() . ($pdf ? '.pdf' : '.docx')
        );

        return $response;
    }

    public function documentPdfAction(Request $request, SalesDocument $salesDocument = null)
    {
        return $this->documentAction($request, $salesDocument, true);
    }

    public function deleteAction(Request $request, SalesDocument $salesDocument = null)
    {
        if($salesDocument->isFacture() || $salesDocument->isAvoir()){
            $this->addFlash('danger', 'Il est interdit de supprimer une facture ou un avoir.');
            return $this->redirectToRoute('crm_billing_salesdocument_index');
        }
        foreach ($salesDocument->getDetails() AS $detail) {
            $detail->setSalesDocument(null);
            $this->getDoctrine()->getManager()->remove($detail);
        }
        $salesDocument->setCustomer(null);
        $this->getDoctrine()->getManager()->remove($salesDocument);

        $this->getDoctrine()->getManager()->flush($salesDocument);


        return $this->redirectToRoute('crm_billing_salesdocument_index');
    }

    public function editAccount(Request $request)
    {
        $form = $this->createFormBuilder();
        $form->add('amount');

        $form = $form->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        }
    }
}

