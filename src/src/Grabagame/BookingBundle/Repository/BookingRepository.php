<?php

namespace Grabagame\BookingBundle\Repository;

use Doctrine\ORM\EntityRepository,
    Grabagame\BookingBundle\Entity\BookingCollection;

/**
 * BookingRepository
 */
class BookingRepository extends EntityRepository
{
    /**
     * @param Club $club
     * @param DateTime $date
     *
     * @return Collection
     */
    public function getBookingsByDate($club, $date)
    {
        $startDate = $date->format('Y-m-d 00:00:00');
        $endDate = $date->format('Y-m-d 23:59:59');

        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT b
            FROM GrabagameBookingBundle:Booking b
            WHERE b.startTime BETWEEN :startDate AND :endDate 
            AND b.club = :club"
        );

        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        $query->setParameter('club', $club);

        return $query->getResult();
    }

    /**
     * @param Club  $club
     * @param Court $court
     * @param array $startTimes
     *
     * @return Collection
     */
    public function findByStartTimes($club, $court, $startTimes)
    {
        $formattedStartTime = array();
        foreach ($startTimes as $startTime) {
            $formattedStartTime[] = $startTime->format('Y-m-d G:i:s');
        }

        $formattedStartTime = "'".implode("','", $formattedStartTime)."'";

        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT b
            FROM GrabagameBookingBundle:Booking b
            WHERE b.startTime IN (".$formattedStartTime.")
            AND b.court = :court
            AND b.club = :club"
        );

        $query->setParameter('court', $court);
        $query->setParameter('club', $club);

        return $query->getResult();
    }

    /**
     * @param Booking $booking
     * 
     * @return Booking
     */
    public function findNextBooking($booking)
    {
        $startTime = $booking->getStartTime()->format('Y-m-d G:i:s');
        $endTime = $booking->getStartTime()->format('Y-m-d 23:59:59');
        
        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT b
            FROM GrabagameBookingBundle:Booking b
            WHERE b.startTime BETWEEN :startTime AND :endTime
            AND b.court = :court
            AND b.club = :club
            ORDER BY b.startTime ASC"
        );

        $query->setParameter('startTime', $startTime);
        $query->setParameter('endTime', $endTime);
        $query->setParameter('court', $booking->getCourt());
        $query->setParameter('club', $booking->getClub());
        $query->setMaxResults(1);
        
        return $query->getOneOrNullResult();
    }

    /**
     * @param Booking $booking
     *
     * @return Booking
     */
    public function findPreviousBooking($booking)
    {
        $startTime = $booking->getStartTime()->format('Y-m-d 00:00:00');
        $endTime = $booking->getStartTime()->format('Y-m-d G:i:s');

        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT b
            FROM GrabagameBookingBundle:Booking b
            WHERE b.startTime BETWEEN :startTime AND :endTime
            AND b.court = :court
            AND b.club = :club
            ORDER BY b.startTime ASC"
        );

        $query->setParameter('startTime', $startTime);
        $query->setParameter('endTime', $endTime);
        $query->setParameter('court', $booking->getCourt());
        $query->setParameter('club', $booking->getClub());
        $query->setMaxResults(1);
        
        return $query->getOneOrNullResult();
    }
}