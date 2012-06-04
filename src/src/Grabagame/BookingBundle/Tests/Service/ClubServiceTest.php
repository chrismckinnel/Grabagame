<?php

namespace Grabagame\BookingBundle\Tests\Service;

use Grabagame\BookingBundle\Tests\DatabaseTestCase,
    Grabagame\BookingBundle\Entity\Club,
    Grabagame\BookingBundle\Entity\Court,
    Grabagame\BookingBundle\Entity\Member,
    Grabagame\BookingBundle\Entity\Booking,
    Grabagame\BookingBundle\Entity\BookingCollection,
    Grabagame\BookingBundle\Entity\BookingOnBehalf;

class ClubServiceTest extends DatabaseTestCase
{
    /** 
     * @var Doctrine\ORM\EntityManager 
     */
    private $em;
    private $bookingService;
    
    /**
     * Set up
     */
    public function setUp() {
        parent::setUp();
        $this->em = $this->getEntityManager();
        $client = $this->getClient();

        $this->clubService = $client->getContainer()->get('service.club');
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

    /**
     * Test adding courts to a club
     */
    public function testAddCourts()
    {
        $clubRepo = $this->em->getRepository('GrabagameBookingBundle:Club');
        $club = $clubRepo->find(1);
        $newNumberOfCourts = 5;

        $club = $this->clubService->updateCourts($club, $newNumberOfCourts);

        foreach ($club->getCourts() as $court) {
            $this->assertThat($court, $this->isInstanceOf('Grabagame\BookingBundle\Entity\Court'));
        }

        $this->assertEquals(5, $club->getNumberOfCourts());
    }

    /**
     * Test removing courts without bookings
     */
    public function testRemoveCourtWithoutBookings()
    {
        $clubRepo = $this->em->getRepository('GrabagameBookingBundle:Club');
        $club = $clubRepo->find(1); $newNumberOfCourts = 2;

        $club = $this->clubService->updateCourts($club, $newNumberOfCourts);

        foreach ($club->getCourts() as $court) {
            $this->assertThat($court, $this->isInstanceOf('Grabagame\BookingBundle\Entity\Court'));
        }

        $this->assertEquals(2, $club->getNumberOfCourts());
    }

    /**
     * Test removing courts with bookings
     */
    public function testRemoveCourtWithBookings()
    {
        $clubRepo = $this->em->getRepository('GrabagameBookingBundle:Club');
        $club = $clubRepo->find(1); $newNumberOfCourts = 1;

        $club = $this->clubService->updateCourts($club, $newNumberOfCourts);

        foreach ($club->getCourts() as $court) {
            $this->assertThat($court, $this->isInstanceOf('Grabagame\BookingBundle\Entity\Court'));
        }

        $this->assertEquals(1, $club->getNumberOfCourts());
    }
}
