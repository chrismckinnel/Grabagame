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
     * @param Court $court
     *
     * @return BookingCollection
     */
    public function getBookingsByDate($club, $date)
    {
        $startDate = $date->format('Y-m-d 00:00:00');
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT b 
            FROM GrabagameBookingBundle:Booking p 
            WHERE b.startDate = :date
            AND p.Status <> \'DRAFT\'
            AND p.Status <> \'DECLINED\'');
        $query->setParameter('query', '%'.$searchTerm.'%');
        return $query->getArrayResult();        
    }
}