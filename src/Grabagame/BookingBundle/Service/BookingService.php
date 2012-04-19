<?php
namespace Grabagame\BookingBundle\Service;

use Monolog\Handler\StreamHandler,
    Grabagame\BookingBundle\Entity\Booking;

 /**
  * Booking service
  *
  * @package Grabagame\BookingBundle\Service
  * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
  */
class BookingService extends LoggerAware {

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
    private function saveBooking($booking)
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
     *
     * @return Booking
     */
    public function createBooking($court, $member, $startTime)
    {
        $booking = new Booking();
        $booking->setCourt($court);
        $booking->setMember($member);
        $booking->setStartTime($startTime);

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
        
    }

    /**
     * @param Booking $booking
     */
    public function cancelBooking($booking)
    {
        $entityManager = $this->doctrine->getEntityManager();
        $entityManager->remove($booking);
        $entityManager->flush();
    }
}
