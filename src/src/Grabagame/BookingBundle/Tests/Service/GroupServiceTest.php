<?php

namespace Grabagame\BookingBundle\Tests\Service;

use Grabagame\BookingBundle\Tests\DatabaseTestCase,
    Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken,
    Grabagame\BookingBundle\Entity\Group;

class GroupServiceTest extends DatabaseTestCase
{
    /** 
     * @var Doctrine\ORM\EntityManager 
     */
    private $em;
    private $groupService;
    private $client;
    
    /**
     * Set up
     */
    public function setUp() {
        parent::setUp();
        $this->em = $this->getEntityManager();
        $this->client = $this->getClient();

        $this->groupService = $this->client->getContainer()->get('service.group');
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
     * Test saving a group
     */
    public function testSave()
    {
        $groupRepo = $this->em->getRepository('GrabagameBookingBundle:Group');

        $newGroup = new Group();
        $newGroup->setActive(true);
        $newGroup->setNumberOfMembers(10);

        $savedGroup = $this->groupService->saveGroup($newGroup);
        $savedGroup = $groupRepo->find($savedGroup->getId());

        $this->assertTrue($savedGroup->isActive());
        $this->assertTrue($savedGroup->getNumberOfMembers() == 10);
    }

    /**
     * Test getting a group by ID
     */
    public function testGetById()
    {
        $groupRepo = $this->em->getRepository('GrabagameBookingBundle:Group');

        $group = $this->groupService->getGroupById(1);

        $this->assertTrue($group->isActive());
        $this->assertEquals('Administrators', $group->getName());
    }

    /**
     * Test deactivate group
     */
    public function testDeactivate()
    {
        $groupRepo = $this->em->getRepository('GrabagameBookingBundle:Group');
        $group = $groupRepo->find(1);

        $this->assertTrue($group->isActive());
        $this->groupService->deactivate($group);
        $this->assertFalse($group->isActive());
    }

    /**
     * Test activate group
     */
    public function testActivate()
    {
        $groupRepo = $this->em->getRepository('GrabagameBookingBundle:Group');
        $group = $groupRepo->find(2);

        $this->assertFalse($group->isActive());
        $this->groupService->activate($group);
        $this->assertTrue($group->isActive());
    }

    /**
     * Test get number of members in a group
     *
     * @dataProvider getDataForGetNumberOfMembers
     */
    public function testGetNumberOfMembers($groupId, $expectedNumber)
    {
        $groupRepo = $this->em->getRepository('GrabagameBookingBundle:Group');

        $group = $groupRepo->find($groupId);
        $this->assertEquals($expectedNumber, $this->groupService->getNumberOfMembers($group));
    }

    public function getDataForGetNumberOfMembers()
    {
        return array(
            array(1, 2),
            array(2, 1),
            array(3, 0),
        );
    }
    
    /**
     * Test get all groups
     */
    public function testGetAllGroups()
    {
        $groupRepo = $this->em->getRepository('GrabagameBookingBundle:Group');

        $groups = $this->groupService->getAllGroups();
        $this->assertEquals(3, count($groups));
    }
}
