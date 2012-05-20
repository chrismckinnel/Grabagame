<?php

namespace Grabagame\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert;
    
/**
 * Booking on behalf of entity
 *
 * @ORM\Entity()
 * @ORM\Table( name="booking_on_behalf" )
 */
class BookingOnBehalf
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")    
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(message="Please enter a first name.", groups={"Registration", "Profile"})
     * @Assert\MinLength(limit="3", message="The first name you entered is too short.", groups={"Registration", "Profile"})
     * @Assert\MaxLength(limit="100", message="The first name you entered is too long.", groups={"Registration", "Profile"})     
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(message="Please enter a last name.", groups={"Registration", "Profile"})
     * @Assert\MinLength(limit="3", message="The last name you entered is too short.", groups={"Registration", "Profile"})
     * @Assert\MaxLength(limit="100", message="The last name you entered is too long.", groups={"Registration", "Profile"})     
     */
    private $lastName;

    /**
     * Unidirectional - one-to-one
     *
     * @ORM\OneToOne(targetEntity="Booking")
     */
    private $booking;

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
     * Set firstName
     *
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
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
     * Set booking
     *
     * @param Grabagame\BookingBundle\Entity\Booking $booking
     */
    public function setBooking(\Grabagame\BookingBundle\Entity\Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get booking
     *
     * @return Grabagame\BookingBundle\Entity\Booking 
     */
    public function getBooking()
    {
        return $this->booking;
    }

    /**
     * @return string
     */
    public function getNameForBookingTable()
    {
        return strtoupper(substr($this->getFirstName(), 0, 1)).'. '.$this->getLastName();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}
