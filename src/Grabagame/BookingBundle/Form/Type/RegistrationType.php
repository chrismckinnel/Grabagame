<?php

namespace Grabagame\BookingBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationType extends BaseType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('plainPassword')
            ->remove('username')
            ->remove('email')
            ->add('firstName', 'text', array('label' => 'First name'))
            ->add('lastName', 'text', array('label' => 'Last name'))
            ->add('username')
            ->add('email', 'email')
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'required' => true,
                'invalid_message' => 'The password fields must match',
                'options' => array('label' => 'Password'),
                'first_name' => 'Password',
                'second_name' => 'Verify password',
            )
        );
    }

    public function getName()
    {
        return 'grabagame_user_registration';
    }
}
