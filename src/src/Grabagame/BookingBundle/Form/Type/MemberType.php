<?php

namespace Grabagame\BookingBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

/**
 * Member type
 *
 * @package GrabagameBookingBundle
 * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
 */
class MemberType extends AbstractType
{

    /**
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Grabagame\BookingBundle\Entity\Member',
        );
    }

    /**
     * @param FormBuilder $builder Form builder
     * @param array       $options Options array
     *
     * @return void
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('groups', 'entity', array(
            'class' => 'Grabagame\BookingBundle\Entity\Group',
            'label' => 'Group: ',
            'multiple' => true,
            'property' => 'name',
            'expanded' => true,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'member';
    }
}
