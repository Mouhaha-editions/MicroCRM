<?php

namespace BillingBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalesDocumentProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', null, [
                'required' => false,
                'label' => 'sales_document_product.label.reference',
                'attr' => [
                    'placeholder' => 'sales_document_product.placeholder.reference',
                    'class' => 'form_label input-sm',
//                    'data-bind' => 'value: label',

                ]
            ])
            ->add('label', null, [
                'required' => true,
                'label' => 'sales_document_product.label.label',
                'attr' => [
                    'placeholder' => 'sales_document_product.placeholder.label',
                    'class' => 'form_label input-sm',
//                    'data-bind' => 'value: label',

                ]
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => 'sales_document_product.label.description',

                'attr' => [
                    'placeholder' => 'sales_document_product.placeholder.description',
                    'class' => 'form_description input-sm',
//                    'data-bind' => 'value: description',

                ]
            ])
            ->add('duration', null, [
                'required' => false,
                'label' => 'sales_document_product.label.duration',
                'attr' => [
                    'class' => 'form_duration input-sm',
                    'placeholder' => 'sales_document_product.placeholder.duration',

//                    'data-bind' => 'value: taxes, valueUpdate: "afterkeydown"',

                ]
            ])
            ->add('unitAmountHt', null, [
                'required' => true,
                'label' => 'sales_document_product.label.unitAmountHt',
                'attr' => [
                    'class' => 'form_unitAmountHt input-sm',
//                    'data-bind' => 'value: unitPrice, valueUpdate: "afterkeydown"',
                ]
            ])

            ->add('taxes', null, [
                'required' => true,
                'label' => 'sales_document_product.label.taxes',
                'attr' => [
                    'class' => 'form_taxes input-sm',
//                    'data-bind' => 'value: taxes, valueUpdate: "afterkeydown"',

                ]
            ])
            ->add('taxesToApply', null, [
                'required' => true,
                'label' => 'sales_document_detail.label.taxesToApply',
                'attr' => [
                    'class' => 'form_taxesToApply input-sm',
                    'placeholder' => 'sales_document_detail.placeholder.taxesToApply',

                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BillingBundle\Entity\SalesDocumentProduct',
            'user' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'BillingBundle_sales_document_product';
    }
}
