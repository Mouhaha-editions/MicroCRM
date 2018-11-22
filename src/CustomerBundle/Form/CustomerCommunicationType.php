<?php

namespace CustomerBundle\Form;

use CustomerBundle\Enums\ECustomerCommunicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerCommunicationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customerCommunicationType', ChoiceType::class, [
                'choices' => [
                    ECustomerCommunicationType::ToString(ECustomerCommunicationType::EMAIL) => ECustomerCommunicationType::EMAIL,
                    ECustomerCommunicationType::ToString(ECustomerCommunicationType::TELEPHONE) => ECustomerCommunicationType::TELEPHONE,
                    ECustomerCommunicationType::ToString(ECustomerCommunicationType::MOBILE) => ECustomerCommunicationType::MOBILE,
                    ECustomerCommunicationType::ToString(ECustomerCommunicationType::WEBSITE) => ECustomerCommunicationType::WEBSITE,
                    ECustomerCommunicationType::ToString(ECustomerCommunicationType::FACEBOOK) => ECustomerCommunicationType::FACEBOOK,
                    ECustomerCommunicationType::ToString(ECustomerCommunicationType::SKYPE) => ECustomerCommunicationType::SKYPE,
                    ECustomerCommunicationType::ToString(ECustomerCommunicationType::FAX) => ECustomerCommunicationType::FAX,
                    ECustomerCommunicationType::ToString(ECustomerCommunicationType::UNDEFINED) => ECustomerCommunicationType::UNDEFINED,
                ],
                'choice_translation_domain' => 'messages',
                'label' => 'customer_bundle.forms.customer_communication.label.customerCommunicationType',
                'placeholder' => 'customer_bundle.forms.customer_communication.placeholder.customerCommunicationType',

            ])
            ->add('value', null, [
                'label' => 'customer_bundle.forms.customer_communication.label.value',
                'attr'=>['placeholder' => 'customer_bundle.forms.customer_communication.placeholder.value',],
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CustomerBundle\Entity\CustomerCommunication'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'customerbundle_customercommunication';
    }


}
