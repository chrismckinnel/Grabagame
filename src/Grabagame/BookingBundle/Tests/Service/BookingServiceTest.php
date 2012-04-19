<?php

namespace Grabagame\BookingBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

    }
}
