<?php

namespace BillingBundle\Form;

use BillingBundle\Entity\SalesDocument;
use BillingBundle\Entity\SalesDocumentDetail;
use BillingBundle\Entity\SalesDocumentPayment;
use CustomerBundle\Entity\Customer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalesDocumentType extends AbstractType
{
const MENSU = 100;
const IMMEDIAT = 200;
const AUCUN = 300;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', HiddenType::class, [
                "mapped" => false
            ])
            ->add('customer', EntityType::class, [
                'class'=>Customer::class,
                'attr' => ['placeholder' => "sales_document.placeholder.client"],
                'label' => 'sales_document.label.client',
                'required' => true,
                'choice_label'=>function($t){return $t->getLastName()." ".$t->getFirstName();},
                'query_builder'=> function(EntityRepository $em){
                return $em->createQueryBuilder('c')
                    ->orderBy('c.lastName')
                    ->addOrderBy('c.firstName');
                }

            ])
            ->add('comment', TextareaType::class, [
                'attr' => ['placeholder' => "sales_document.placeholder.comment"],
                'label' => 'sales_document.label.comment',
                'required' => false,

            ])
            ->add('state', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    "Bon de commande" => SalesDocument::BON_COMMANDE,
                    "Facture" => SalesDocument::FACTURE,
                    "Avoir" => SalesDocument::AVOIR,
                ],
                'attr' => array('placeholder' => "sales_document.placeholder.state"),
                'label' => 'sales_document.label.state'
            ])
            ->add('details',
                CollectionType::class, [
                    'entry_type' => SalesDocumentDetailType::class,
                    'required' => true,
                    'entry_options' => array('label' => false),
                    'allow_add' => true,
                    'delete_empty' => true,
                    'allow_delete' => true,
            ])
            ->add('Enregistrer', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BillingBundle\Entity\SalesDocument',
            'user' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'BillingBundle_sales_document';
    }
}
