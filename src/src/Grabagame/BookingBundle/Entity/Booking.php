<?php

namespace Grabagame\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Booking entity
 *
 * @ORM\Entity(repositoryClass="Grabagame\BookingBundle\Repository\BookingRepository")
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
     * @ORM\Column(type="string")
     */
    private $type = 'normal';

    /**
     * @ORM\Column(type="boolean")
     */
    private $nameHidden = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdDate;

    /**
     * Bidirectional - many-to-one
     *
     * @ORM\ManyToOne(targetEntity="Club", inversedBy="bookings")     
     */
    private $club;

    /**
     * Bidirectional - one-to-one
     *
     * @ORM\OneToOne(targetEntity="BookingOnBehalf")
     */
    private $bookingOnBehalf;

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
     * Constructor
     */
    public function __construct()
    {
        $this->createdDate = new \DateTime('now');
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
     * Set type
     *
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name hidden
     *
     * @param boolean $nameHidden
     */
    public function setNameHidden($nameHidden)
    {
        $this->nameHidden = $nameHidden;
    }

    /**
     * Get nameHidden
     *
     * @return integer 
     */
    public function isNameHidden()
    {
        return $this->nameHidden;
    }

    /**
     * Set club
     *
     * @param Grabagame\BookingBundle\Entity\Club $club
     */
    public function setClub(\Grabagame\BookingBundle\Entity\Club $club)
    {
        $this->club = $club;
    }

    /**
     * Get club
     *
     * @return Grabagame\BookingBundle\Entity\Club 
     */
    public function getClub()
    {
        return $this->club;
    }

    /**
     * Set booking on behalf
     *
     * @param Grabagame\BookingBundle\Entity\BookingOnBehalf $bookingOnBehalf
     */
    public function setBookingOnBehalf(\Grabagame\BookingBundle\Entity\BookingOnBehalf $bookingOnBehalf)
    {
        $this->bookingOnBehalf = $bookingOnBehalf;
    }

    /**
     * Get booking on behalf
     *
     * @return Grabagame\BookingBundle\Entity\BookingOnBehalf 
     */
    public function getBookingOnBehalf()
    {
        return $this->bookingOnBehalf;
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

    /**
     * @return array
     */
    public function getAllSlots()
    {
        $slots = array();
        $startTime = clone $this->getStartTime();
        $minuteIncrement = $this->getClub()->getBookingIncrement();

        for ($i = 0; $i < $this->getSlots(); $i++) {
            $slots[] = clone $startTime;
            $startTime = $startTime->add(new \DateInterval('PT'.$minuteIncrement.'M'));
        }

        return $slots;
    }
}