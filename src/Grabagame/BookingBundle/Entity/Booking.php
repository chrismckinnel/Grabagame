<?php

namespace Grabagame\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Booking entity
 *
 * @ORM\Entity(repositoryClass="Grabagame\BookingBundle\Entity\Booking")
 * @ORM\Table( name="booking" )
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")    
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startTime;

    /**
     * @ORM\Column(type="integer")
     */
    private $slots = 1;

    /**
     * Bidirectional - many-to-one
     *
     * @ORM\ManyToOne(targetEntity="Court", inversedBy="bookings")     
     */
    private $court;

    /**
     * Bidirectional - many-to-one
     *
     * @ORM\ManyToOne(targetEntity="Member", inversedBy="bookings")     
     */
    private $member;


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
     * Set start time
     *
     * @param datetime $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * Get start time
     *
     * @return datetime 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set slots
     *
     * @param integer $slots
     */
    public function setSlots($slots)
    {
        $this->slots = $slots;
    }

    /**
     * Get slots
     *
     * @return integer 
     */
    public function getSlots()
    {
        return $this->slots;
    }

    /**
     * Set court
     *
     * @param Grabagame\BookingBundle\Entity\Court $court
     */
    public function setCourt(\Grabagame\BookingBundle\Entity\Court $court)
    {
        $this->court = $court;
    }

    /**
     * Get court
     *
     * @return Grabagame\BookingBundle\Entity\Court 
     */
    public function getCourt()
    {
        return $this->court;
    }

    /**
     * Set member
     *
     * @param Grabagame\BookingBundle\Entity\Member $member
     */
    public function setMember(\Grabagame\BookingBundle\Entity\Member $member)
    {
        $this->member = $member;
    }

    /**
     * Get member
     *
     * @return Grabagame\BookingBundle\Entity\Member 
     */
    public function getMember()
    {
        return $this->member;
    }
}