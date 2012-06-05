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
     * Test saving a club
     */
    public function testSaveClub()
    {
        $clubRepo = $this->em->getRepository('GrabagameBookingBundle:Club');

        $newClub = new Club();
        $newClub->setName('TestAddClub');
        $newClub->setEmail('testemail@gmail.com');
        $firstBookingTime = new \DateTime('2012-01-01 06:00:00');
        $newClub->setFirstBookingTime($firstBookingTime);
        $newClub->setBookingIncrement(60);
        $newClub->setMaxSlots(3);
        $newClub->setActive(true);

        $savedClub = $this->clubService->saveClub($newClub);
        $savedClub = $clubRepo->find($savedClub->getId());

        $this->assertTrue($savedClub->isActive());
        $this->assertEquals('TestAddClub', $savedClub->getName());
        $this->assertEquals('testemail@gmail.com', $savedClub->getEmail());
        $this->assertEquals($firstBookingTime, $savedClub->getFirstBookingTime());
        $this->assertEquals('60', $savedClub->getBookingIncrement());
        $this->assertEquals('3', $savedClub->getMaxSlots());
    }

    /**
     * Test get club by ID
     */
    public function testGetClubById()
    {
        $clubRepo = $this->em->getRepository('GrabagameBookingBundle:Club');

        $club = $this->clubService->getClubById(1);

        $this->assertTrue($club->isActive());
        $this->assertEquals('Test club', $club->getName());
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
