<?php

namespace Grabagame\BookingBundle\Tests\Service;

use Grabagame\BookingBundle\Tests\DatabaseTestCase,
    Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class MemberServiceTest extends DatabaseTestCase
{
    /** 
     * @var Doctrine\ORM\EntityManager 
     */
    private $em;
    private $memberService;
    private $client;
    
    /**
     * Set up
     */
    public function setUp() {
        parent::setUp();
        $this->em = $this->getEntityManager();
        $this->client = $this->getClient();

        $this->memberService = $this->client->getContainer()->get('service.member');
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
     * Test get logged in member
     */
    public function testGetLoggedInMember()
    {
        $memberRepo = $this->em->getRepository('GrabagameBookingBundle:Member');
        $member = $memberRepo->find(1);

        $token = new UsernamePasswordToken($member, null, 'main', $member->getRoles());
        $securityContext = $this->client->getContainer()->get('security.context');
        $securityContext->setToken($token);

        $expectedMember = $member;
        $returnedMember = $this->memberService->getLoggedInMember();

        $this->assertEquals($expectedMember, $returnedMember);
    }

    /**
     * Test get logged in member - no one logged in
     *
     * @expectedException Grabagame\BookingBundle\Exception\MemberException
     */
    public function testNobodyLoggedIn()
    {
        $this->memberService->getLoggedInMember();
    }
}
