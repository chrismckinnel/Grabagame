<?php

namespace Grabagame\BookingBundle\Tests\Entity;

use Grabagame\BookingBundle\Tests\DatabaseTestCase,
    Grabagame\BookingBundle\Entity\Booking,
    Grabagame\BookingBundle\Service\BookingService;

/**
 * BookingRespository Test
 *
 * @package Grabagame\BookingBundle\Tests\Entity
 * @author  Chris McKinnel <chrismckinnel@gmail.com>
 */
class BookingRepositoryTest extends DatabaseTestCase
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /**
     * Set up entity manager
     */
    public function setUp()
    {
        parent::setUp();
        $this->em = $this->getEntityManager();
    }

    /**
     * Test find next booking
     */
    public function testFindNextBooking()
    {
        $repo = $this->em->getRepository('GrabagameBookingBundle:Booking');

        $booking = $repo->find(1);
        $nextBooking = $repo->findNextBooking($booking);

        $this->assertEquals('2012-05-03 10:00:00', $nextBooking->getStartTime()->format('Y-m-d G:i:s'));
        $this->assertNotEquals('4', $nextBooking->getId());
    }

    /**
     * @param 
     * 
     * @return void
     */
    public function testGetBookingsByDate()
    {
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');
        $clubRepo = $this->em->getRepository('GrabagameBookingBundle:Club');
       
        $club = $clubRepo->find(1); 
        $date = new \DateTime("2012-05-03");
        
        $bookings = $bookingRepo->getBookingsByDate($club, $date);

        $expectedBookings = array();
        $expectedBookings[] = $bookingRepo->find(1);
        $expectedBookings[] = $bookingRepo->find(2);

        $this->assertEquals($expectedBookings, $bookings);

        $date = new \DateTime("2012-05-04");
        
        $bookings = $bookingRepo->getBookingsByDate($club, $date);

        $expectedBookings = array();
        $expectedBookings[] = $bookingRepo->find(3);
        $expectedBookings[] = $bookingRepo->find(4);

        $this->assertEquals($expectedBookings, $bookings);
    }

    /**
     * Test find by start times
     */
    public function testFindByStartTimes()
    {
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');
        $clubRepo = $this->em->getRepository('GrabagameBookingBundle:Club');
        $courtRepo = $this->em->getRepository('GrabagameBookingBundle:Court');
        $club = $clubRepo->find(1); 
        $court = $courtRepo->find(2);

        $expectedBookings = array();
        $expectedBookings[] = $bookingRepo->find(1);
        $expectedBookings[] = $bookingRepo->find(2);

        $startTimes = array(
            new \DateTime('2012-05-03 06:00:00'),
            new \DateTime('2012-05-03 10:00:00'),
            new \DateTime('2012-05-04 09:00:00'),
        );

        $bookings = $bookingRepo->findByStartTimes($club, $court, $startTimes);

        $this->assertEquals($expectedBookings, $bookings);

        $expectedBookings = array();
        $startTimes = array(
            new \DateTime('2012-05-01 06:00:00'),
            new \DateTime('2012-05-08 10:00:00'),
            new \DateTime('2012-05-09 09:00:00'),
        );

        $bookings = $bookingRepo->findByStartTimes($club, $court, $startTimes);

        $this->assertEquals($expectedBookings, $bookings);
    }

    /**
     * Provides the data set for dbunit
     *
     * @return \PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return $this->getYamlDataSet('bookings.yml');
    }
}

