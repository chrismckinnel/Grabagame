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
     * @param 
     *
     * @expectedException Exception
     */
    public function testCreateBookingFail()
    {
        $booking = $this->bookingService->createBooking();
    }
}
