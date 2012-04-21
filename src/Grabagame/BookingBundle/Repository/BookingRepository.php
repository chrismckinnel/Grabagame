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
            WHERE b.startTime BETWEEN '".$startDate."' AND '".$endDate."'
            AND b.club = :club"
        );

        $query->setParameter('club', $club);

        return $query->getResult();
    }
}