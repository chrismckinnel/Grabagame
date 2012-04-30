<?php

namespace Grabagame\BookingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Booking collection
 */
class BookingCollection extends ArrayCollection
{
    private $bookingKeys = array();
    private $startTimeFormat = 'Y-m-d G:i:s';

    /**
     * @param 
     *
     * @return void
     */
    private function makeBookingKey($court, $startTime)
    {
        return $court->getNumber().'_'.$startTime->format($this->startTimeFormat);
    }

    /**
     * @param Collection $bookingCollection
     */
    public function __construct($bookings = array())
    {
        parent::__construct($bookings);

        foreach ($bookings as $booking) {
            $startTime = $booking->getStartTime();
            $court = $booking->getCourt();

            $this->bookingKeys[$this->makeBookingKey($court, $startTime)] = $booking;

            if ($booking->getSlots() > 1) {
                for ($i = 2; $i <= $booking->getSlots(); $i++) {
                    $nextSlotTime = $booking->getStartTime()
                                            ->add(new \DateInterval('PT1H'));

                    $this->bookingKeys[$this->makeBookingKey($court, $nextSlotTime)] = $booking;
                }
            }
        }
    }

    /**
     * @param Court    $court     Court object
     * @param DateTime $startTime DateTime object
     *
     * @return Booking
     */
    public function getForCourtAndStartTime($court, \DateTime $startTime)
    {
        $key = $this->makeBookingKey($court, $startTime);

        if (array_key_exists($key, $this->bookingKeys)) {
            return $this->bookingKeys[$key];
        }

        return null;
    }
}
