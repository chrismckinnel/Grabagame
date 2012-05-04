<?php

namespace Labour\CampaignDbBundle\Tests\Entity;

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

        $booking = $repo->findById(1);
        $nextBooking = $repo->findNextBooking($booking);

        $this->assertEquals('2012-05-03 10:00:00', $nextBooking->getStartTime()->format('Y-m-d G:i:s'));
        $this->assertNotEquals('4', $nextBooking->getId());
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

