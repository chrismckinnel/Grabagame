<?php

namespace Grabagame\BookingBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

/**
 * Group type for Tangent User Bundle
 *
 * @package GrabagameBookingBundle
 * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
 */
class GroupType extends AbstractType
{

    /**
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Grabagame\BookingBundle\Entity\Group',
        );
    }

    /**
     * @param FormBuilder $builder Form builder object
     * @param array       $options Options array
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('name', 'text', array('label' => 'Group name: '));
        $builder->add('roles', 'choice', array(
            'choices' => array(
                'ROLE_USER' => 'ROLE_USER',
                'ROLE_ADMIN' => 'ROLE_ADMIN'),
            'multiple' => true,
            'expanded' => true,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'group';
    }
}
