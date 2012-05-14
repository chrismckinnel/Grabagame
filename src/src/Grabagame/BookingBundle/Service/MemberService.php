<?php
namespace Grabagame\BookingBundle\Service;

use Monolog\Handler\StreamHandler,
    Grabagame\BookingBundle\Exception\MemberException;

 /**
  * Club service
  *
  * @package Grabagame\BookingBundle\Service
  * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
  */
class MemberService extends LoggerAware {

    protected $doctrine;
    protected $securityContext;
    protected $logger;
    
    /**
     * @param Registry $doctrine
     */
    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param SecurityContext $securityContext
     */
    public function setSecurityContext($securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @return User
     */
    public function getLoggedInMember()
    {
        $token = $this->securityContext->getToken();
        if ($token != null) {
            return $token->getUser();
        } else {
            throw new MemberException('You must be logged in to perform this action.');
        }
    }

    /**
     * @return array
     */
    public function getAllMembers()
    {
        return $this->doctrine
                    ->getEntityManager()
                    ->getRepository('GrabagameBookingBundle:Member')
                    ->findAll();
    }    

    /**
     * @param integer $memberId Member ID
     *
     * @return array
     */
    public function getMemberById($memberId)
    {
        return $this->doctrine
                    ->getEntityManager()
                    ->getRepository('GrabagameBookingBundle:Member')
                    ->find($memberId);
    }

    /**
     * @param string $query
     *
     * @return array
     */
    public function searchMembers($query)
    {
        return $this->doctrine
                    ->getEntityManager()
                    ->getRepository('GrabagameBookingBundle:Member')
                    ->findAllBySearch($query);
    }    
}
