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
use Symfony\Component\HttpFoundation\JsonResponse;
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
class ResourcesController extends Controller
{
    public function jsonListAction(Request $request)
    {
        $entities_pure = $this->getDoctrine()->getRepository('CustomerBundle:Customer')->createQueryBuilder('c')
            ->select('c')
            ->orderBy('c.companyName', 'ASC')
            ->addOrderBy('c.lastName', 'ASC')
            ->getQuery()->getResult();
        $entities = [];
        /** @var Customer $ent */
        foreach ($entities_pure AS $ent) {
            $entities[] = [
                'label' => $ent->getFullName(),
                'id' => $ent->getId()
            ];
        }
        return new JsonResponse($entities);
    }
}
