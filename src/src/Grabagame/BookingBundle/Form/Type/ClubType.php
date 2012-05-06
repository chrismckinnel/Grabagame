<?php

namespace Grabagame\BookingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form type for a club
 *
 * @package Grabagame\BookingBundle\Form\Type
 * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
 */
class ClubType extends AbstractType
{

    /**
     * @param array $options Options array
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Grabagame\BookingBundle\Entity\Club',
        );
    }

    /**
     * @param FormBuilder $builder Form builder
     * @param array       $options Options array
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'text', array('label' => 'Club name: '));
        $builder->add('email', 'text', array('label' => 'Email address: '));
        $builder->add('bookingIncrement', 'text', array('label' => 'Booking increment: '));
        $builder->add('numberOfCourts', 'text', array('label' => 'Number of courts: '));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'club';
    }
}
