<?php
namespace Grabagame\BookingBundle\Service;

use Monolog\Handler\StreamHandler,
    Grabagame\BookingBundle\Entity\Booking,
    Grabagame\BookingBundle\Exception\BookingException,
    Grabagame\BookingBundle\Entity\BookingCollection;


 /**
  * Booking service
  *
  * @package Grabagame\BookingBundle\Service
  * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
  */
class BookingService extends LoggerAware {

    protected $doctrine;
    protected $memberService;
    protected $logger;
    
    /**
     * @param Registry $doctrine
     */
    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param Registry $memberService
     */
    public function setMemberService($memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * @param Booking $booking
     *
     * @return boolean
     */
    private function canManageBooking($booking)
    {
        $member = $this->memberService->getLoggedInMember();
        if ($member->hasRole('ROLE_ADMIN') || $member === $booking->getMember()) {
            return true;   
        } else {
            return false;   
        }
    }

    /**
     * @param integer $booking_id
     *
     * @return $booking
     */
    public function getBookingById($booking_id)
    {
        $booking = $this->doctrine
                        ->getEntityManager()
                        ->getRepository('GrabagameBookingBundle:Booking')
                        ->find($booking_id);

        if (empty($booking)) {
            throw new BookingException('Sorry that booking does not exist.');
        }

        return $booking;
    }

    /**
     * @param Booking $booking
     *
     * @return $booking
     */
    public function saveBooking($booking)
    {
        $entityManager = $this->doctrine->getEntityManager();
        $entityManager->persist($booking);
        $entityManager->flush();

        return $booking;
    }

    /**
     * @param Court    $court     Court object
     * @param Member   $member    Member object
     * @param DateTime $startTime Time the booking starts
     * @param slots    $slots     Amount of slots
     *
     * @return Booking
     */
    public function createBooking($court, $member, $startTime, $slots = 1)
    {
        $booking = new Booking();
        $booking->setCourt($court);
        $booking->setMember($member);
        $booking->setStartTime($startTime);
        $booking->setSlots($slots);

        return $booking;
    }

    /**
     * @param Booking $booking
     *
     * @return Booking
     */
    public function moveBooking($booking)
    {
        if ($this->isSlotAvailable()) {
            $this->saveBooking($booking);
        } else {
            throw new BookingException('That booking slot is not available');
        }

        return $booking;
    }

    /**
     * @param Booking $booking
     *
     * @return boolean
     */
    public function isSlotAvailable($booking)
    {
        $club = $booking->getClub();
        $bookingIncrement = $club->getBookingIncrement();
        $court = $booking->getCourt();

        $startTimes = array();
        $startTime = clone $booking->getStartTime();

        for ($i = 0; $i < $booking->getSlots(); $i++) {
            $startTimes[] = $booking->getStartTime();
            $startTime = $startTime->add(new \DateInterval('PT'.$bookingIncrement.'M'));
        }

        $bookings = $this->doctrine
                         ->getEntityManager()
                         ->getRepository('GrabagameBookingBundle:Booking')
                         ->findByStartTimes($club, $court, $startTimes);

        return empty($bookings) ? true : false;
    }

    /**
     * @param Booking $booking
     */
    public function cancelBooking($booking)
    {
        if ($this->canManageBooking($booking)) {
            $entityManager = $this->doctrine->getEntityManager();
            $entityManager->remove($booking);
            $entityManager->flush();
        } else {
            $member = $this->memberService->getLoggedInMember();
            $this->logger->info('Security alert');
            $this->logger->info('User ID: '.$member->getId.'('.$member->getFirstName().' '.$member->getLastName().' just tried to cancel another members booking');
            throw new AccessDeniedException('Nice try, but you can only cancel your own bookings.');
        }
    }

    /**
     * @param Club     $club Club object
     * @param DateTime $date Date to get bookings for
     * 
     * @return BookingCollection
     */
    public function getBookingsByDate($club, $date)
    {
        $bookings = $this->doctrine
                         ->getEntityManager()
                         ->getRepository('GrabagameBookingBundle:Booking')
                         ->getBookingsByDate($club, $date);

        $bookingCollection = ($bookings) ? new BookingCollection($bookings) : new BookingCollection();
        
        return $bookingCollection;
    }
}
