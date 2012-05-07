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
     * @param Member  $member
     * @param Booking $booking
     *
     * @return boolean
     */
    private function canManageBooking($booking, $member)
    {
        if ($member->hasRole('ROLE_ADMIN') || 
            $member->hasRole('CAN_CANCEL_BOOKINGS') ||
            $member === $booking->getMember()) {
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
    public function isSlotAvailable(\Grabagame\BookingBundle\Entity\Booking $booking)
    {
        $club = $booking->getClub();
        $bookingIncrement = $club->getBookingIncrement();
        $court = $booking->getCourt();

        $previousBooking = $this->doctrine
                                ->getEntityManager()
                                ->getRepository('GrabagameBookingBundle:Booking')
                                ->findPreviousBooking($booking);

        $startTimes = array();

        if ($previousBooking != null) {
            $startTime = clone $previousBooking->getStartTime();

            for ($i = 0; $i < $previousBooking->getSlots(); $i++) {
                $startTimes[] = clone $startTime;
                $startTime = $startTime->add(new \DateInterval('PT'.$bookingIncrement.'M'));
            }
        }

        if (in_array($booking->getStartTime(), $startTimes)) {
            return false;
        }

        $nextBooking = $this->doctrine
                            ->getEntityManager()
                            ->getRepository('GrabagameBookingBundle:Booking')
                            ->findNextBooking($booking);

        $nextBookingStartTimes = array();
        $currentBookingStartTimes = array();

        if ($nextBooking != null) {
            $startTime = clone $nextBooking->getStartTime();

            for ($i = 0; $i < $nextBooking->getSlots(); $i++) {
                $startTimes[] = clone $startTime;
                $startTime = $startTime->add(new \DateInterval('PT'.$bookingIncrement.'M'));
            }
        }

        foreach ($nextBookingStartTimes as $nextBookingStartTime) {
            foreach ($currentBookingStartTimes as $currentBookingStartTime) {
                if (in_array($currentBookingStartTime, $nextBookingStartTimes)) {
                    return false;
                }
            }
        }

        $currentBooking = $this->doctrine
                               ->getEntityManager()
                               ->getRepository('GrabagameBookingBundle:Booking')
                               ->findBookingByStartTime($booking);

        return ($currentBooking == null) ? true : false;
    }

    /**
     * @param Booking $booking
     * @param Member  $member
     */
    public function cancelBooking($booking, $member)
    {
        if ($this->canManageBooking($booking, $member)) {
            $entityManager = $this->doctrine->getEntityManager();
            $entityManager->remove($booking);
            $entityManager->flush();
        } else {
            $this->logger->info('Security alert');
            $this->logger->info('User ID: '.$member->getId().'('.$member->getFirstName().' '.$member->getLastName().' just tried to cancel another members booking');
            throw new BookingException('Nice try, but you can only cancel your own bookings.');
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

    /**
     * @param Booking $booking
     * 
     * @return integer
     */
    public function getMaxSlots($booking)
    {
        $nextBooking = $this->doctrine
                            ->getEntityManager()
                            ->getRepository('GrabagameBookingBundle:Booking')
                            ->findNextBooking($booking);

        $startTime = $booking->getStartTime();

        if (!empty($nextBooking)) {
            $bookingIncrement = $booking->getClub()->getBookingIncrement();
    
            $timeDifference = $startTime->diff($nextBooking->getStartTime(), true);
            $hours = $timeDifference->format('%h');
            $minutes = $timeDifference->format('%i');

            if ($hours > 0) {
                if ($minutes > 0) {
                    $minutes = $hours * $minutes;
                } else {
                    $minutes = $hours * 60;
                }
            }             

            $maxSlots = $minutes / $bookingIncrement;
        } else {
            $maxSlots = 99;
        }

        return $maxSlots;
    }

    /**
     * @param string $dayToDisplay
     * 
     * @return DateTime
     */
    public function getDayToDisplay($dayToDisplay)
    {
        $dayToDisplay = ($dayToDisplay == null) ? "now" : $dayToDisplay;
        return new \DateTime($dayToDisplay);
    }

    /**
     * @param string $dayToDisplay
     * 
     * @return DateTime
     */
    public function getYesterday($dayToDisplay)
    {
        $tempDayToDisplay = clone $dayToDisplay;
        return $tempDayToDisplay->sub(new \DateInterval('P1D'));
    }

    /**
     * @param string $dayToDisplay
     * 
     * @return DateTime
     */
    public function getTomorrow($dayToDisplay)
    {
        $tempDayToDisplay = clone $dayToDisplay;
        return $tempDayToDisplay->add(new \DateInterval('P1D'));
    }

    /**
     * @param BookingOnBehalf $bookingOnBehalf
     * 
     * @return BookingOnBehalf
     */
    public function saveBookingOnBehalf($bookingOnBehalf)
    {
        $entityManager = $this->doctrine->getEntityManager();
        $entityManager->persist($bookingOnBehalf);
        $entityManager->flush();

        return $bookingOnBehalf;
    }
}
