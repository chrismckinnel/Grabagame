<?php

namespace Grabagame\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Security\Core\Role\RoleInterface,
    FOS\UserBundle\Entity\Group AS GroupBase;

/**
 * Group Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="member_group")
 *
 * @package GrabagameBookingBundle
 * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
 */
class Group extends GroupBase
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean", name="active")
     */
    private $active = true;
    private $numberOfMembers;

    /**
     * @param string $name  Name of the group
     * @param array  $roles Array of roles
     */
    public function __construct($name = '', $roles = array())
    {
        parent::__construct($name, $roles);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     *
     * @return boolean
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return integer
     */
    public function getNumberOfMembers()
    {
        return $this->numberOfMembers;
    }

    /**
     * @param integer $numberOfMembers
     */
    public function setNumberOfMembers($numberOfMembers)
    {
        $this->numberOfMembers = $numberOfMembers;
    }
}
