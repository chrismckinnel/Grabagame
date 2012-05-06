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

    /**
     * @param integer $clubId
     *
     * @return $club
     */
    public function getClubById($clubId)
    {
        $club = $this->doctrine
                     ->getEntityManager()
                     ->getRepository('GrabagameBookingBundle:Club')
                     ->find($clubId);

        if (!$club) {
            throw new \Exception('No club exists with the ID '.$clubId);
        }

        return $club;
    }

    
    /**
     * @param Club $club
     *
     * @return $club
     */
    public function saveClub($club)
    {
        $entityManager = $this->doctrine->getEntityManager();
        $entityManager->persist($club);
        $entityManager->flush();

        return $club;
    }

    /**
     * @param Club     $club
     * @param DateTime $today
     *
     * @return array
     */
    public function getStartTimes($club, $today)
    {
        $startTimes = array();
        $startTime = $club->getFirstBookingTime();
        $increment = $club->getBookingIncrement();
        
        $currentTime = new \DateTime($today->format('Y-m-d').$startTime->format(' H:i:s'));
        $finishTime = new \DateTime($today->format('Y-m-d 23:59:59'));

        while ($currentTime < $finishTime) {
            $startTimes[] = new \DateTime($currentTime->format('Y-m-d G:i:s'));
            $currentTime->add(new \DateInterval('PT'.$increment.'M'));
        }
    
        return $startTimes;
    }

}
