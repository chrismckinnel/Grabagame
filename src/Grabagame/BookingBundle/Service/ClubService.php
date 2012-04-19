<?php
namespace Grabagame\BookingBundle\Service;

use Monolog\Handler\StreamHandler;

 /**
  * Club service
  *
  * @package Grabagame\BookingBundle\Service
  * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
  */
class ClubService extends LoggerAware {

    protected $doctrine;
    protected $logger;
    
    /**
     * @param Registry $doctrine
     */
    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param string  $name             Name of the club
     * @param string  $email            Email address of the club administrator
     * @param integer $bookingIncrement Time increment for a booking
     *
     * @return Club
     */
    public function addClub($name, $email, $bookingIncrement)
    {
        $club = new Club();
        $club->setName($name);
        $club->setEmail($email);
        $club->setBookingIncrement($bookingIncrement);

        return $club;
    }
}
