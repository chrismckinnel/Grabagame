<?php
namespace Grabagame\BookingBundle\Service;

use Monolog\Handler\StreamHandler,
    Grabagame\BookingBundle\Entity\Club,
    Grabagame\BookingBundle\Entity\Court;

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
     * @return array
     */
    public function getAllClubs()
    {
        return $this->doctrine
                    ->getEntityManager()
                    ->getRepository('GrabagameBookingBundle:Club')
                    ->findAll();
    }

    /**
     * @param Club    $club        Club object
     * @param integer $courtNumber Court number
     *
     * @return Court
     */
    public function getCourtByNumber($club, $courtNumber)
    {
        return $this->doctrine
                    ->getEntityManager()
                    ->getRepository('GrabagameBookingBundle:Court')
                    ->findCourtByClubAndCourtNumber($club, $courtNumber);
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

    /**
     * @param integer $clubId
     *
     * @return Club
     */
    public function activate($clubId)
    {
        $club = $this->getClubById($clubId);
        $club->setActive(true);
        $club = $this->saveClub($club);

        return $club;
    }

    /**
     * @param integer $clubId
     *
     * @return Club
     */
    public function deactivate($clubId)
    {
        $club = $this->getClubById($clubId);
        $club->setActive(false);
        $club = $this->saveClub($club);

        return $club;
    }    

    /**
     * @param Club    $club           Club object
     * @param integer $numberOfCourts Number of courts
     *
     * @return Club
     */
    public function updateCourts($club, $numberOfCourts)
    {
        if ($numberOfCourts < $club->getNumberOfCourts()) {
            for ($i = $club->getNumberOfCourts(); $i > $numberOfCourts; $i--) {
                $this->removeCourtByNumber($club, $i);
            }
        } else {
            for ($i = $club->getNumberOfCourts()+1; $i <= $numberOfCourts; $i++) {
                $court = new Court();
                $court->setNumber($i);
                $court->setClub($club);
                $club->addCourt($court);
            }
        }

        return $club;
    }

    /**
     * @param Club    $club        Club object
     * @param integer $courtNumber Court number to remove
     *
     * @return Club
     */
    public function removeCourtByNumber($club, $courtNumber)
    {
        $court = $this->getCourtByNumber($club, $courtNumber);

        $entityManager = $this->doctrine->getEntityManager();
        $entityManager->remove($court);
        $entityManager->flush();  

        return $club;
    }
}
