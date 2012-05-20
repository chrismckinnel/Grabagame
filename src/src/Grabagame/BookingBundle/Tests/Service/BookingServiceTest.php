<?php

namespace Grabagame\BookingBundle\Tests\Service;

use Grabagame\BookingBundle\Tests\DatabaseTestCase,
    Grabagame\BookingBundle\Entity\Club,
    Grabagame\BookingBundle\Entity\Court,
    Grabagame\BookingBundle\Entity\Member,
    Grabagame\BookingBundle\Entity\Booking,
    Grabagame\BookingBundle\Entity\BookingCollection,
    Grabagame\BookingBundle\Entity\BookingOnBehalf;

class BookingServiceTest extends DatabaseTestCase
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
     * Provides the data set for dbunit
     *
     * @return \PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return $this->getYamlDataSet('bookings.yml');
    }

    /**
     * @param Court    $court
     * @param DateTime $startTime
     * @param integer  $slots
     *
     * @return Booking
     */
    private function makeNewBooking($club, $court, $startTime, $slots, $member)
    {
        $clubRepo = $this->em->getRepository('GrabagameBookingBundle:Club');
        $courtRepo = $this->em->getRepository('GrabagameBookingBundle:Court');
        $memberRepo = $this->em->getRepository('GrabagameBookingBundle:Member');
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');

        $club = $clubRepo->find($club);
        $court = $courtRepo->find($court);
        $member = $memberRepo->find($member);

        $booking = new Booking();
        $booking->setClub($club);
        $booking->setCourt($court);
        $booking->setMember($member);
        $booking->setStartTime($startTime);
        $booking->setSlots($slots);
        
        return $booking;
    }

    /**
     * Test get max slots function
     */
    public function testGetMaxSlots()
    {
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');

        $booking = $this->makeNewBooking(1, 2, new \DateTime('2012-05-03 12:00:00'), 1, 1);
        $this->assertEquals(3, $this->bookingService->getMaxSlots($booking));

        $booking = $this->makeNewBooking(1, 2, new \DateTime('2012-05-03 09:00:00'), 1, 1);
        $this->assertEquals(1, $this->bookingService->getMaxSlots($booking));

        $booking = $this->makeNewBooking(1, 2, new \DateTime('2012-05-03 06:00:00'), 1, 1);
        $this->assertEquals(3, $this->bookingService->getMaxSlots($booking));

        $booking = $this->makeNewBooking(2, 5, new \DateTime('2012-05-10 08:00:00'), 2, 4);
        $this->assertEquals(4, $this->bookingService->getMaxSlots($booking));

        $booking = $this->makeNewBooking(2, 5, new \DateTime('2012-05-10 12:00:00'), 2, 4);
        $this->assertEquals(3, $this->bookingService->getMaxSlots($booking));
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
     * Test cancelling an on behalf booking
     */
    public function testAdminCancelBookingOnBehalf()
    {
        $memberRepo = $this->em->getRepository('GrabagameBookingBundle:Member');
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');

        $member = $memberRepo->find(1);
        $booking = $bookingRepo->find(7);
        
        $this->bookingService->cancelBooking($booking, $member);
        $this->assertEquals(null, $bookingRepo->find(7));
    }

    /**
     * Test save booking
     */
    public function testSaveBooking()
    {
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');
        
        $booking = $this->makeNewBooking(1, 1, new \DateTime('now'), 2, 1);
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
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');

        $booking = $this->makeNewBooking(1, 2, new \DateTime('2012-05-03 09:00:00'), 2, 1);
        $this->assertEquals(false, $this->bookingService->isSlotAvailable($booking));

        $booking = $this->makeNewBooking(1, 2, new \DateTime('2012-05-03 09:00:00'), 1, 1);
        $this->assertEquals(true, $this->bookingService->isSlotAvailable($booking));

        $booking = $this->makeNewBooking(1, 2, new \DateTime('2012-05-03 06:00:00'), 1, 1);
        $this->assertEquals(false, $this->bookingService->isSlotAvailable($booking));

        $booking = $this->makeNewBooking(1, 2, new \DateTime('2012-05-03 07:00:00'), 1, 1);
        $this->assertEquals(false, $this->bookingService->isSlotAvailable($booking));

        $booking = $this->makeNewBooking(1, 2, new \DateTime('2012-05-03 07:00:00'), 3, 1);
        $this->assertEquals(false, $this->bookingService->isSlotAvailable($booking));

        $booking = $this->makeNewBooking(1, 2, new \DateTime('2012-05-03 12:00:00'), 3, 1);
        $this->assertEquals(true, $this->bookingService->isSlotAvailable($booking));
    }

    /**
     * Test get yesterday function
     */
    public function testGetYesterday()
    {
        $today = new \DateTime('2012-05-03');
        $expectedYesterday = new \DateTime('2012-05-02');

        $yesterday = $this->bookingService->getYesterday($today);
        $this->assertEquals($expectedYesterday, $yesterday);
    }

    /**
     * Test get tomorrow function
     */
    public function testGetTomorrow()
    {
        $today = new \DateTime('2012-05-03');
        $expectedTomorrow = new \DateTime('2012-05-04');

        $tomorrow = $this->bookingService->getTomorrow($today);
        $this->assertEquals($expectedTomorrow, $tomorrow);
    }

    /**
     * Test get bookings by date
     */
    public function testGetBookingsByDate()
    {
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');
        $clubRepo = $this->em->getRepository('GrabagameBookingBundle:Club');
        $club = $clubRepo->find(1);
       
        $expectedBookings = array(
            $bookingRepo->find(1),
            $bookingRepo->find(2),
        );

        $returnedBookings = $this->bookingService->getBookingsByDate($club, new \DateTime("2012-05-03"));

        $this->assertEquals($expectedBookings, $returnedBookings);

        $expectedBookings = array(
            $bookingRepo->find(5),
            $bookingRepo->find(6),
            $bookingRepo->find(7),
        );

        $returnedBookings = $this->bookingService->getBookingsByDate($club, new \DateTime("2012-05-10"));

        $this->assertEquals($expectedBookings, $returnedBookings);
    }

    /**
     * Test get bookings by date failing
     */
    public function testGetBookingsByDateFailing()
    {
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');
        $clubRepo = $this->em->getRepository('GrabagameBookingBundle:Club');
        $club = $clubRepo->find(1);
       
        $expectedBookings = array(
            $bookingRepo->find(3),
            $bookingRepo->find(2),
        );

        $returnedBookings = $this->bookingService->getBookingsByDate($club, new \DateTime("2012-05-03"));

        $this->assertNotEquals($expectedBookings, $returnedBookings);
    }

    /**
     * Test get booking slots for bookings
     */
    public function testGetBookedSlotsForBookings()
    {
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');
       
        $bookings = array(
            $bookingRepo->find(1),
            $bookingRepo->find(2),
        );

        $expectedBookedSlots = new BookingCollection($bookings);
        $returnedBookedSlots = $this->bookingService->getBookedSlotsForBookings($bookings);

        $this->assertEquals($expectedBookedSlots, $returnedBookedSlots);

        $returnedBookedSlots = $this->bookingService->getBookedSlotsForBookings();
        $this->assertEquals(new BookingCollection(), $returnedBookedSlots);
    }

    /**
     * Test get booking slots for bookings failing
     */
    public function testGetBookedSlotsForBookingsFailing()
    {
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');
       
        $bookings = array(
            $bookingRepo->find(1),
            $bookingRepo->find(2),
        );

        $randomBookings = array(
            $bookingRepo->find(4),
            $bookingRepo->find(6),
        );

        $expectedBookedSlots = new BookingCollection($randomBookings);
        $returnedBookedSlots = $this->bookingService->getBookedSlotsForBookings($bookings);

        $this->assertNotEquals($expectedBookedSlots, $returnedBookedSlots);
    }

    /**
     * Test move booking
     */
    public function testMoveBooking()
    {
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');
        
        $bookingToMove = $bookingRepo->find(1);
        $newStartTime = new \DateTime('2012-05-03 12:00:00');
        $bookingToMove->setStartTime($newStartTime);

        $this->bookingService->moveBooking($bookingToMove);

        $movedBooking = $bookingRepo->find(1);
        $this->assertEquals($newStartTime, $movedBooking->getStartTime());
    }

    /**
     * Test move booking failing
     *
     * @expectedException Grabagame\BookingBundle\Exception\BookingException
     */
    public function testMoveBookingFails()
    {
        $bookingRepo = $this->em->getRepository('GrabagameBookingBundle:Booking');
        
        $bookingToMove = $bookingRepo->find(1);
        $newStartTime = new \DateTime('2012-05-03 09:00:00');
        $bookingToMove->setStartTime($newStartTime);

        $this->bookingService->moveBooking($bookingToMove);
    }

    /**
     * Test save booking on behalf
     */
    public function testSaveBookingOnBehalf()
    {
        $booking = $this->makeNewBooking(2, 5, new \DateTime('2012-01-01 06:00:00'), 2, 4);
        $this->bookingService->saveBooking($booking);

        $bookingOnBehalf = new BookingOnBehalf();
        $bookingOnBehalf->setFirstName('Test');
        $bookingOnBehalf->setLastName('User');
        $bookingOnBehalf->setBooking($booking);

        $bookingOnBehalf = $this->bookingService->saveBookingOnBehalf($bookingOnBehalf);

        $this->assertEquals('T. User', $bookingOnBehalf->getNameForBookingTable());
        $this->assertEquals('Test User', $bookingOnBehalf->getFullName());
        $this->assertEquals(2, $bookingOnBehalf->getId());
    }
}
