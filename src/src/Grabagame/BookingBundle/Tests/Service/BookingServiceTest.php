<?php

namespace Grabagame\BookingBundle\Tests\Service;

use Grabagame\BookingBundle\Tests\DatabaseTestCase,
    Grabagame\BookingBundle\Entity\Club,
    Grabagame\BookingBundle\Entity\Court,
    Grabagame\BookingBundle\Entity\Member,
    Grabagame\BookingBundle\Entity\Booking;

class DefaultControllerTest extends DatabaseTestCase
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

        $this->bookingService = $client->getContainer()->get('service.booking');
    }

    /**
     * Test create booking
     */
    public function testCreateBooking()
    {
        $court = new Court();
        $member = new Member();
        $startTime = new \DateTime('now');
        $slots = 2;
        
        $booking = $this->bookingService->createBooking($court, $member, $startTime, $slots);

        $this->assertEquals($court, $booking->getCourt());
        $this->assertEquals($member, $booking->getMember());
        $this->assertEquals($startTime, $booking->getStartTime());
        $this->assertEquals($slots, $booking->getSlots());
    }

    /**
     * Test get day to display
     */
    public function testGetDayToDisplay()
    {
        $input = null;
        $dayToDisplay = $this->bookingService->getDayToDisplay($input);
        $expectedDayToDisplay = new \DateTime('now');
        $expectedDayToDisplay = $expectedDayToDisplay->format('Y-m-d');

        $this->assertEquals($expectedDayToDisplay, $dayToDisplay->format('Y-m-d'));

        $input = '2012-05-04';
        $dayToDisplay = $this->bookingService->getDayToDisplay($input);

        $this->assertEquals('2012-05-04', $dayToDisplay->format('Y-m-d'));
    }

    /**
     * Test cancelling your own booking
     */
    public function testCancelYourOwnBooking()
    {
        $memberRepo = $this->em->getRepository('GrabagameBookingBundle:Member');
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');

        $member = $memberRepo->find(1);
        $booking = $bookingRepo->find(1);

        $this->bookingService->cancelBooking($booking, $member);
        $this->assertEquals(null, $bookingRepo->find(1));
    }

    /**
     * Test a member trying to cancel another members booking 
     *
     * @expectedException Grabagame\BookingBundle\Exception\BookingException
     */
    public function testCancelOtherMembersBooking()
    {
        $memberRepo = $this->em->getRepository('GrabagameBookingBundle:Member');
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');

        $member = $memberRepo->find(2);
        $booking = $bookingRepo->find(1);

        $this->bookingService->cancelBooking($booking, $member);
    }

    /**
     * Test an admin cancelling another persons booking
     */
    public function testAdminCancelBooking()
    {
        $memberRepo = $this->em->getRepository('GrabagameBookingBundle:Member');
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');

        $member = $memberRepo->find(3);
        $booking = $bookingRepo->find(1);

        $this->bookingService->cancelBooking($booking, $member);
        $this->assertEquals(null, $bookingRepo->find(1));
    }

    /**
     * Test save booking
     */
    public function testSaveBooking()
    {
        $clubRepo = $this->em->getRepository('GrabagameBookingBundle:Club');
        $courtRepo = $this->em->getRepository('GrabagameBookingBundle:Court');
        $memberRepo = $this->em->getRepository('GrabagameBookingBundle:Member');
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');

        $club = $clubRepo->find(1);
        $court = $courtRepo->find(1);
        $member = $memberRepo->find(1);
        $startTime = new \DateTime('now');
        $slots = 2;

        $booking = new Booking();
        $booking->setClub($club);
        $booking->setCourt($court);
        $booking->setMember($member);
        $booking->setStartTime($startTime);
        $booking->setSlots($slots);

        $booking = $this->bookingService->saveBooking($booking);
        $this->assertEquals($booking, $bookingRepo->find($booking->getId()));
    }

    /**
     * Test get booking by id
     */
    public function testGetBookingById()
    {
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');

        $expectedBooking = $bookingRepo->find(1);
        $booking = $this->bookingService->getBookingById(1);

        $this->assertEquals($expectedBooking, $booking);
    }

    /**
     * Test get booking by id, booking doesn't exist
     *
     * @expectedException Grabagame\BookingBundle\Exception\BookingException
     */
    public function testGetBookingByIdDoesNotExist()
    {
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');
        $booking = $this->bookingService->getBookingById(234);
    }

    /**
     * Test is slot available function
     */
    public function testIsSlotAvailable()
    {
        $clubRepo = $this->em->getRepository('GrabagameBookingBundle:Club');
        $courtRepo = $this->em->getRepository('GrabagameBookingBundle:Court');
        $memberRepo = $this->em->getRepository('GrabagameBookingBundle:Member');
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');

        $club = $clubRepo->find(1);
        $court = $courtRepo->find(2);
        $member = $memberRepo->find(1);

        $booking = new Booking();
        $booking->setClub($club);
        $booking->setCourt($court);
        $booking->setMember($member);

        $booking->setStartTime(new \DateTime('2012-05-03 06:00:00'));
        $booking->setSlots(1);
        $this->assertEquals(false, $this->bookingService->isSlotAvailable($booking));

        $booking->setStartTime(new \DateTime('2012-05-03 07:00:00'));
        $booking->setSlots(1);
        $this->assertEquals(false, $this->bookingService->isSlotAvailable($booking));

        $booking->setStartTime(new \DateTime('2012-05-03 07:00:00'));
        $booking->setSlots(3);
        $this->assertEquals(false, $this->bookingService->isSlotAvailable($booking));

        $booking->setStartTime(new \DateTime('2012-05-03 09:00:00'));
        $booking->setSlots(1);
        $this->assertEquals(true, $this->bookingService->isSlotAvailable($booking));
    }

    /**
     * Test move booking function
     */
    public function testMoveBooking()
    {
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');
        $booking = $bookingRepo->find(1);

        $booking->setStartTime(new \DateTime('2012-05-03 07:00:00'));
        $booking->setSlots(1);
    }

    /**
     * Test get max slots function
     */
    public function testGetMaxSlots()
    {
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');
        $booking = $bookingRepo->find(1);

        $booking->setStartTime(new \DateTime("2012-05-03 12:00:00"));
        $this->assertEquals(99, $this->bookingService->getMaxSlots($booking));

        $booking->setStartTime(new \DateTime("2012-05-03 09:00:00"));
        $this->assertEquals(1, $this->bookingService->getMaxSlots($booking));

        $booking->setStartTime(new \DateTime("2012-05-03 06:00:00"));
        $this->assertEquals(0, $this->bookingService->getMaxSlots($booking));
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
