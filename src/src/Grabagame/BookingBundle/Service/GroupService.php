<?php
namespace Grabagame\BookingBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Group service
 *
 * @package GrabagameBookingBundle
 * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
 */
class GroupService extends LoggerAware
{

    protected $logger;
    protected $doctrine;

    /**
     * @param Registry $doctrine
     */
    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param Group $group
     *
     * @return group
     */
    public function saveGroup($group)
    {
        $entityManager = $this->doctrine->getEntityManager();
        $entityManager->persist($group);
        $entityManager->flush();

        return $group;
    }

    /**
     * @return array
     */
    public function getAllGroups()
    {
        return $this->doctrine
                    ->getEntityManager()
                    ->getRepository('GrabagameBookingBundle:Group')
                    ->findAll();
    }

    /**
     * @param integer $id
     *
     * @return Group
     */
    public function getGroupById($id)
    {
        return $this->doctrine
                    ->getEntityManager()
                    ->getRepository('GrabagameBookingBundle:Group')
                    ->find($id);
    }

    /**
     * @param Group $group
     *
     * @return Group
     */
    public function activate($group)
    {
        $group->setActive(true);
        $group = $this->saveGroup($group);

        return $group;
    }

    /**
     * @param Group $group
     *
     * @return Group
     */
    public function deactivate($group)
    {
        $group->setActive(false);
        $group = $this->saveGroup($group);

        return $group;
    }

    /**
     * @param Group $group
     *
     * @return integer
     */
    public function getNumberOfMembers($group)
    {
        $memberArray =  $this->doctrine
                             ->getEntityManager()
                             ->getRepository('GrabagameBookingBundle:Member')
                             ->findUserByGroup($group);
        $numberOfMembers = count($memberArray);

        return $numberOfMembers;
    }
}
