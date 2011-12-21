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
     * @var datetime $time
     */
    private $time;

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
     * Set time
     *
     * @param datetime $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * Get time
     *
     * @return datetime 
     */
    public function getTime()
    {
        return $this->time;
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