<?php

namespace Grabagame\BookingBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase,
    Grabagame\BookingBundle\Entity\Court,
    Grabagame\BookingBundle\Entity\Member;

class DefaultControllerTest extends WebTestCase
{

    private $bookingService;
    
    /**
     * Set up
     */
    public function setUp() {
        $client = static::createClient();
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
}
