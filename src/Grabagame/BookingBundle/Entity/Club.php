<?php

namespace Grabagame\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Grabagame\BookingBundle\Entity\Club
 */
class Club
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $email
     */
    private $email;

    /**
     * @var date $createdDate
     */
    private $createdDate;

    /**
     * @var integer $bookingIncrement
     */
    private $bookingIncrement;

    /**
     * @var Grabagame\BookingBundle\Entity\Court
     */
    private $courts;

    /**
     * @var Grabagame\BookingBundle\Entity\User
     */
    private $users;

    public function __construct()
    {
        $this->courts = new \Doctrine\Common\Collections\ArrayCollection();
    $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set createdDate
     *
     * @param date $createdDate
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }

    /**
     * Get createdDate
     *
     * @return date 
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set bookingIncrement
     *
     * @param integer $bookingIncrement
     */
    public function setBookingIncrement($bookingIncrement)
    {
        $this->bookingIncrement = $bookingIncrement;
    }

    /**
     * Get bookingIncrement
     *
     * @return integer 
     */
    public function getBookingIncrement()
    {
        return $this->bookingIncrement;
    }

    /**
     * Add courts
     *
     * @param Grabagame\BookingBundle\Entity\Court $courts
     */
    public function addCourt(\Grabagame\BookingBundle\Entity\Court $courts)
    {
        $this->courts[] = $courts;
    }

    /**
     * Get courts
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getCourts()
    {
        return $this->courts;
    }

    /**
     * Add users
     *
     * @param Grabagame\BookingBundle\Entity\User $users
     */
    public function addUser(\Grabagame\BookingBundle\Entity\User $users)
    {
        $this->users[] = $users;
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }
}