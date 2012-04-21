<?php

namespace Grabagame\BookingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Booking collection
 */
class BookingCollection extends ArrayCollection
{
    private $bookingKeys;
    private $startTimeFormat = 'Y-m-d G:i:s';

    /**
     * @param Collection $bookingCollection
     */
    public function __construct($bookings = array())
    {
        foreach ($bookings as $booking) {
            $courtNumber = $booking->getCourt()->getNumber();
            $startTime = $booking->getStartTime()->format($this->startTimeFormat);

            $this->bookingKeys[$courtNumber.'_'.$startTime] = $booking;

            if ($booking->getSlots() > 1) {
                for ($i = 2; $i <= $booking->getSlots(); $i++) {
                    $nextSlotTime = $booking->getStartTime()
                                            ->add(new \DateInterval('PT1H'))
                                            ->format($this->startTimeFormat);

                    $this->bookingKeys[$courtNumber.'_'.$nextSlotTime] = $booking;
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
    public function getForCourtAndStartTime($court, $startTime)
    {
        if (!empty($this->bookingKeys)) {
            $courtNumber = $court->getNumber();
            $startTime = $startTime->format($this->startTimeFormat);

            if (array_key_exists($courtNumber.'_'.$startTime, $this->bookingKeys)) {
                return $this->bookingKeys[$courtNumber.'_'.$startTime];
            }
        }

        return null;
    }
}
