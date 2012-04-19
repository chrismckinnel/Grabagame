<?php

namespace Grabagame\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Grabagame\BookingBundle\Entity\Booking
 */
class Booking
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var datetime $startTime
     */
    private $startTime;

    /**
     * @var integer $slots
     */
    private $slots = 1;

    /**
     * @var Grabagame\BookingBundle\Entity\Court
     */
    private $court;

    /**
     * @var Grabagame\BookingBundle\Entity\Member
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