<?php

namespace Grabagame\BookingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Booking collection
 */
class BookingCollection extends ArrayCollection
{
    private $bookingKeys;

    /**
     * @param Collection $bookingCollection
     */
    public function __construct($bookingCollection)
    {
        foreach ($bookingCollection as $booking) {
            $this->bookingKeys[$booking->getCourt().'_'.$booking->getStartTime()] = $booking;

            if ($booking->getSlots() > 1) {
                for ($i = 2; $i <= $booking->getSlots(); $i++) {
                    //add an increment to the start time & save another key
                }
            }
        }
    }

    /**
     * @param Court    $court Court object
     * @param DateTime $startTime DateTime object
     *
     * @return Booking
     */
    public function getForCourtAndStartTime($court, $startTime)
    {
        if (array_key_exists($court .'_'.$startTime, $this->bookingKeys)) {
            return $this->bookingKeys[$court.'_'.$startTime];
        }

        return null;
    }
}
