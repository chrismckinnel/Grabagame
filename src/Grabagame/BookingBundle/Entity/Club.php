<?php

namespace Grabagame\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Grabagame\BookingBundle\Entity\Court;

/**
 * Club entity
 *
 * @ORM\Entity()
 * @ORM\Table( name="club" )
 */
class Club
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")    
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $firstBookingTime;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $bookingIncrement;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxSlots;

    /**
     * Bidirectional - one-to-many
     *
     * @ORM\OneToMany(targetEntity="Court", mappedBy="club", cascade={"persist"})     
     */
    private $courts;

    /**
     * @var integer
     */
    private $numberOfCourts;

    /**
     * Bidirectional - one-to-many
     *
     * @ORM\OneToMany(targetEntity="Member", mappedBy="club")     
     */
    private $club;

    /**
     * Bidirectional - one-to-many
     *
     * @ORM\OneToMany(targetEntity="Member", mappedBy="club")     
     */
    private $members;

    public function __construct()
    {
        $this->courts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->members = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdDate = new \DateTime('now');
        $this->firstBookingTime = new \DateTime('2012-01-01 10:00:00');
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
     * @return stringGrabagame\BookingBundle\Entity\Court 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set firstBookingTime
     *
     * @param string $firstBookingTime
     */
    public function setFirstBookingTime($firstBookingTime)
    {
        $this->firstBookingTime = $firstBookingTime;
    }

    /**
     * Get firstBookingTime
     *
     * @return string
     */
    public function getFirstBookingTime()
    {
        return $this->firstBookingTime;
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
     * Set maxSlots
     *
     * @param integer $maxSlots
     */
    public function setMaxSlots($maxSlots)
    {
        $this->maxSlots = $maxSlots;
    }

    /**
     * Get maxSlots
     *
     * @return integer 
     */
    public function getMaxSlots()
    {
        return $this->maxSlots;
    }

    /**
     * Set numberOfCourts
     *
     * @param integer $numberOfCourts
     */
    public function setNumberOfCourts($numberOfCourts)
    {
        for ($i = 1; $i <= $numberOfCourts; $i++) {
            $court = new Court();
            $court->setNumber($i);
            $court->setClub($this);
            $this->addCourt($court);
        }
    }

    /**
     * Get numberOfCourts
     *
     * @return integer 
     */
    public function getNumberOfCourts()
    {
        return count($this->courts);
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
     * Add members
     *
     * @param Grabagame\BookingBundle\Entity\Member $members
     */
    public function addMember(\Grabagame\BookingBundle\Entity\Member $members)
    {
        $this->members[] = $members;
    }

    /**
     * Get members
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Add bookings
     *
     * @param Grabagame\BookingBundle\Entity\Booking $bookings
     */
    public function addBookings(\Grabagame\BookingBundle\Entity\Booking $booking)
    {
        $this->bookings[] = $bookings;
    }

    /**
     * Get bookings
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getBookings()
    {
        return $this->bookings;
    }

    /**
     * @param integer $courtNumber
     * 
     * @return Court
     */
    public function getCourtByNumber($courtNumber)
    {
        foreach ($this->courts as $court) {
            if ($court->getNumber() == $courtNumber) {
                return $court;
            }
        }

        throw new \Exception('Court number '.$courtNumber.' doesn\'t exist for this club');
    }
}