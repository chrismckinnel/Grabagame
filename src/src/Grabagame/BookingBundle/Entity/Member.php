<?php

namespace Grabagame\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    FOS\UserBundle\Entity\User as BaseUser,
    Symfony\Component\Validator\Constraints as Assert;
    
/**
 * Member entity
 *
 * @ORM\Entity(repositoryClass="Grabagame\BookingBundle\Repository\MemberRepository")
 * @ORM\Table( name="member" )
 */
class Member extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")    
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(message="Please enter your first name.", groups={"Registration", "Profile"})
     * @Assert\MinLength(limit="3", message="The first name you entered is too short.", groups={"Registration", "Profile"})
     * @Assert\MaxLength(limit="100", message="The first name you entered is too long.", groups={"Registration", "Profile"})     
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(message="Please enter your last name.", groups={"Registration", "Profile"})
     * @Assert\MinLength(limit="3", message="The last name you entered is too short.", groups={"Registration", "Profile"})
     * @Assert\MaxLength(limit="100", message="The last name you entered is too long.", groups={"Registration", "Profile"})     
     */
    private $lastName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $verified = 1;

    /**
     * Bidirectional - one-to-many
     *
     * @ORM\OneToMany(targetEntity="Booking", mappedBy="member")     
     */
    private $bookings;

    /**
     * Bidirectional - many-to-one
     *
     * @ORM\ManyToOne(targetEntity="Club", inversedBy="members")     
     */
    private $club;

    /**
     * Bidirectional - many-to-many
     *
     * @ORM\ManyToMany(targetEntity="Grabagame\BookingBundle\Entity\Group")
     * @ORM\JoinTable(name="grabagame_user_group",
     *      joinColumns={@ORM\JoinColumn(name="member_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;    

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setCreatedDate(new \DateTime("now"));
        $this->bookings = new \Doctrine\Common\Collections\ArrayCollection();
        $this->addRole('ROLE_USER');
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
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Set verified
     *
     * @param boolean $verified
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;
    }

    /**
     * Get verified
     *
     * @return boolean 
     */
    public function getVerified()
    {
        return $this->verified;
    }

    /**
     * Add bookings
     *
     * @param Grabagame\BookingBundle\Entity\Booking $bookings
     */
    public function addBooking(\Grabagame\BookingBundle\Entity\Booking $bookings)
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

    /**
     * @return array
     */
    public function getRoles()
    {
        $groups = parent::getGroups();
        $roles = $this->roles;

        if (count($groups)) {
            foreach ($groups as $group) {
                if ($group->isActive()) {
                    $groupRoles = $group->getRoles();
                    if (count($groupRoles)) {
                        foreach ($groupRoles as $role) {
                            if (!in_array($role, $roles, true)) {
                                $roles[] = $role;
                            }
                        }
                    }
                }
            }
        }

        return $roles;
    }

    /**
     * @param array $groups Groups array
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
    }
}