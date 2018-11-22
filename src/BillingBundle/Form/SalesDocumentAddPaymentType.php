<?php
namespace BillingBundle\Form;

use BillingBundle\Entity\SalesDocumentPayment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalesDocumentAddPaymentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', null, [
                'required' => true,
                'label' => 'sales_document_payment.label.label',
                'attr' => [
                    'placeholder' => 'sales_document_payment.placeholder.label',
                    'class' => 'form_label input-sm',
                ]
            ])
            ->add('comment', null, [
                'required' => false,
                'label' => 'sales_document_payment.label.comment',
                'attr' => [
                    'placeholder' => 'sales_document_payment.placeholder.comment',
                    'class' => 'form_label input-sm',
                ]
            ])
            ->add('date', DateType::class, [
                'format' => DateType::HTML5_FORMAT,
                'required' => false,
                'widget' => 'single_text',
                'label' => 'sales_document_payment.label.date'
            ])
            ->add('type', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    "Mensualisation" => SalesDocumentPayment::TYPE_MENSUALISATION,
                    "Chèque" => SalesDocumentPayment::TYPE_CHEQUE,
                    "Prélèvement" => SalesDocumentPayment::TYPE_PRELEVEMENT,
                    "Virement" => SalesDocumentPayment::TYPE_VIREMENT
                ]])
            ->add('amount', null, [
                'required' => true,
                'label' => 'sales_document_payment.label.amount',
                'attr' => [
                    'placeholder' => 'sales_document_payment.placeholder.amount',
                    'class' => 'form_label input-sm',
                ]
            ])
            ->add('mensualisation', SalesDocumentMensualisationType::class, [
                'required' => false,
                'label' => 'sales_document_payment.label.mensualisation',
                'attr' => [
                    'placeholder' => 'sales_document_payment.placeholder.mensualisation',
                    'class' => 'form_label input-sm',
                ]
            ])
            ->add('state', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    "En attente" => SalesDocumentPayment::STATE_WAITING,
                    "Payé" => SalesDocumentPayment::STATE_PAID,
                    "Non payé" => SalesDocumentPayment::STATE_UNPAID
                ],
                'label' => 'sales_document_payment.label.state',
                'attr' => [
                    'placeholder' => 'sales_document_payment.placeholder.state',
                    'class' => 'form_label input-sm',
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BillingBundle\Entity\SalesDocumentPayment',
            'user' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'BillingBundle_sales_document_payment';
    }
}
