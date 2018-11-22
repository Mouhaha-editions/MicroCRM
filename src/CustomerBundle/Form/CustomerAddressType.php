<?php

namespace CustomerBundle\Form;

use CustomerBundle\Enums\ECustomerAddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerAddressType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customerAdresseType', ChoiceType::class, [
                'choices' => [
                    ECustomerAddressType::ToString(ECustomerAddressType::BASE) => ECustomerAddressType::BASE,
                    ECustomerAddressType::ToString(ECustomerAddressType::BILLING) => ECustomerAddressType::BILLING,
                    ECustomerAddressType::ToString(ECustomerAddressType::DELIVERY) => ECustomerAddressType::DELIVERY,
                    ECustomerAddressType::ToString(ECustomerAddressType::UNDEFINED) => ECustomerAddressType::UNDEFINED,
                ],
                'choice_translation_domain' => 'messages',
                'label' => 'customer_bundle.forms.customer_address.label.customerAddressType',
                'placeholder' => 'customer_bundle.forms.customer_address.placeholder.customerAddressType',


            ])
            ->add('ligne1', null, [
                'label' => 'customer_bundle.forms.customer_address.label.ligne1',
                'attr' => ['placeholder' => 'customer_bundle.forms.customer_address.placeholder.ligne1',],

            ])
            ->add('ligne2', null, [
                'label' => 'customer_bundle.forms.customer_address.label.ligne2',
                'attr' => ['placeholder' => 'customer_bundle.forms.customer_address.placeholder.ligne2',],
                'required' => false,

            ])
            ->add('ligne3', null, [
                'label' => 'customer_bundle.forms.customer_address.label.ligne3',
                'attr' => ['placeholder' => 'customer_bundle.forms.customer_address.placeholder.ligne3',],
                'required' => false,

            ])
            ->add('codePostal', null, [
                'label' => 'customer_bundle.forms.customer_address.label.codePostal',
                'attr' => ['placeholder' => 'customer_bundle.forms.customer_address.placeholder.codePostal',],
                'required'=>true,

            ])
            ->add('ville', null, [
                'label' => 'customer_bundle.forms.customer_address.label.ville',
                'attr' => ['placeholder' => 'customer_bundle.forms.customer_address.placeholder.ville',],
                'required'=>true,

            ])
            ->add('pays', null, [
                'label' => 'customer_bundle.forms.customer_address.label.pays',
                'attr' => ['placeholder' => 'customer_bundle.forms.customer_address.placeholder.pays',],
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CustomerBundle\Entity\CustomerAddress'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'customerbundle_customeraddress';
    }


}
