<?php

namespace Grabagame\BookingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form type for a booking
 *
 * @package Grabagame\BookingBundle\Form\Type
 * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
 */
class BookingType extends AbstractType
{

    /**
     * @param array $options Options array
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Grabagame\BookingBundle\Entity\Booking',
        );
    }

    /**
     * @param FormBuilder $builder Form builder
     * @param array       $options Options array
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('slots', 'choice', array('choices' => array('1' => '1', '2' => '2', '3' => '3')));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'booking';
    }
}
