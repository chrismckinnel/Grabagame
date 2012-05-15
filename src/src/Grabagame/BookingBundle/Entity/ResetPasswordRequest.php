<?php

namespace Grabagame\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reset password request Entity
 *
 * @ORM\Entity
 * @ORM\Table( name="reset_password_request" )
 *
 */
class ResetPasswordRequest
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $uniqueHash;

    /**
     * @ORM\Column(type="datetime")
     * @var datetime
     */
    private $expires;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $email;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUniqueHash()
    {
        return $this->uniqueHash;
    }

    /**
     * @param string $uniqueHash
     */
    public function setUniqueHash($uniqueHash)
    {
        $this->uniqueHash = $uniqueHash;
    }

    /**
     * @return datetime
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @param datetime $expires
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
}
