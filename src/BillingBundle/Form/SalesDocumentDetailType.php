<?php

namespace BillingBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalesDocumentDetailType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', null, [
                'required' => false,
                'label' => 'sales_document_detail.label.reference',
                'attr' => [
                    'placeholder' => 'sales_document_detail.placeholder.reference',
                    'class' => 'form_reference input-sm',
//                    'data-bind' => 'value: label',

                ]
            ])
            ->add('label', null, [
                'required' => true,
                'label' => 'sales_document_detail.label.label',
                'attr' => [
                    'placeholder' => 'sales_document_detail.placeholder.label',
                    'class' => 'form_label input-sm',
//                    'data-bind' => 'value: label',

                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'sales_document_detail.label.description',

                'attr' => [
                    'placeholder' => 'sales_document_detail.placeholder.description',
                    'class' => 'form_description input-sm',
//                    'data-bind' => 'value: description',

                ]
            ])
            ->add('duration', null, [
                'required' => false,
                'label' => 'sales_document_detail.label.duration',
                'attr' => [
                    'placeholder' => 'sales_document_detail.placeholder.duration',
                    'class' => 'form_duration input-sm',
//                    'data-bind' => 'value: label',

                ]
            ])
            ->add('date', DateTimeType::class, [
                'format' => DateTimeType::HTML5_FORMAT,
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['placeholder' => "sales_document_details.placeholder.date"],
                'label' => 'sales_document_detail.placeholder.date'
            ])
            ->add('quantity', null, [
                'required' => true,
                'label' => 'sales_document_detail.label.quantity',
                'attr' => [
                    'class' => 'form_quantity input-sm',
//                    'data-bind' => 'value: quantity, valueUpdate: "afterkeydown"',
                ]
            ])
            ->add('unitAmountHt', null, [
                'required' => true,
                'label' => 'sales_document_detail.label.unitAmountHt',
                'attr' => [
                    'class' => 'form_unitAmountHt input-sm',
//                    'data-bind' => 'value: unitPrice, valueUpdate: "afterkeydown"',
                ]
            ])
            ->add('taxes', null, [
                'required' => true,
                'label' => 'sales_document_detail.label.taxes',
                'attr' => [
                    'class' => 'form_taxes input-sm',
//                    'data-bind' => 'value: taxes, valueUpdate: "afterkeydown"',

                ]
            ])

            ->add('totalAmountTtc', null, [
                'required' => true,
                'label' => 'sales_document_detail.label.totalAmountTtc',
                'attr' => [
                    'class' => 'form_totalAmountTtc input-sm',
//                    'readonly' => 'readonly',
//                    'data-bind' => 'value: totalLine()',
                ]
            ])//            ->add('Ajouter', SubmitType::class)

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BillingBundle\Entity\SalesDocumentDetail',
            'user' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'BillingBundle_sales_document_detail';
    }
}
