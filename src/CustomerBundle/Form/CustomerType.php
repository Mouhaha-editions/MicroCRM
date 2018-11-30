<?php

namespace CustomerBundle\Form;

use CustomerBundle\Enums\ECustomerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customerType', ChoiceType::class, [
                'choices' => [
                    ECustomerType::ToString(ECustomerType::SUSPECT) => ECustomerType::SUSPECT,
                    ECustomerType::ToString(ECustomerType::PROSPECT) => ECustomerType::PROSPECT,
                    ECustomerType::ToString(ECustomerType::CLIENT) => ECustomerType::CLIENT
                ],
                'choice_translation_domain' => 'messages',
                'label' => 'customer_bundle.forms.customer.label.customerType',
                'placeholder' => 'customer_bundle.forms.customer.placeholder.customerType',

            ])
            ->add('firstName', null, [
                'label' => 'customer_bundle.forms.customer.label.firstName',
                'attr' => ['placeholder' => 'customer_bundle.forms.customer.placeholder.firstName',],
            ])
            ->add('lastName', null, [
                'label' => 'customer_bundle.forms.customer.label.lastName',
                'attr' => ['placeholder' => 'customer_bundle.forms.customer.placeholder.lastName',],
                'required' => false,
            ])->add('pointsFidelite', null, [
                'label' => 'customer_bundle.forms.customer.label.pointsFidelite',
                'attr' => ['placeholder' => 'customer_bundle.forms.customer.placeholder.pointsFidelite',],
                'required' => false,
            ])
            ->add('birthday', DateType::class, [
                'label' => 'customer_bundle.forms.customer.label.birthday',
                'attr' => ['placeholder' => 'customer_bundle.forms.customer.placeholder.birthday',],
                'format' => DateType::HTML5_FORMAT,
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('comment', null, [
                'label' => 'customer_bundle.forms.customer.label.comment',
                'attr' => ['placeholder' => 'customer_bundle.forms.customer.placeholder.comment',],
                'required' => false,
            ])
            ->add('companyName', null, [
                'label' => 'customer_bundle.forms.customer.label.companyName',
                'attr' => ['placeholder' => 'customer_bundle.forms.customer.placeholder.companyName',],
                'required' => false,
            ])

            ->add('siret', null, [
                'label' => 'customer_bundle.forms.customer.label.siret',
                'attr' => ['placeholder' => 'customer_bundle.forms.customer.placeholder.siret',],
                'required' => false,
            ])
            ->add('customerCommunications', CollectionType::class, [
                'entry_type' => CustomerCommunicationType::class,
                'required' => true,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'delete_empty' => true,
                'allow_delete' => true,

            ])
            ->add('customerAddresses', CollectionType::class, [
                'entry_type' => CustomerAddressType::class,
                'required' => true,
                'allow_add' => true,
                'delete_empty' => true,
                'allow_delete' => true

            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CustomerBundle\Entity\Customer'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'customerbundle_customer';
    }


}
