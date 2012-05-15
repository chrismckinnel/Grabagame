<?php
namespace Grabagame\BookingBundle\Service;

use Monolog\Handler\StreamHandler,
    Grabagame\BookingBundle\Entity\ResetPasswordRequest,
    Grabagame\BookingBundle\Exception\MemberException;

 /**
  * Club service *
  * @package Grabagame\BookingBundle\Service
  * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
  */
class MemberService extends LoggerAware {

    protected $doctrine;
    protected $securityContext;
    protected $logger;
    protected $mailer;
    
    /**
     * @param Registry $doctrine
     */
    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param Mailer $mailer
     */
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
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
     * @param string $email
     *
     * @return array
     */
    public function getMemberByEmail($email)
    {
        return $this->doctrine
                    ->getEntityManager()
                    ->getRepository('GrabagameBookingBundle:Member')
                    ->findOneByEmail($email);
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

    /**
     * @param Entity $entity
     *
     * @return Entity
     */
    public function saveEntity($entity)
    {
        $entityManager = $this->doctrine->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();

        return $entity;        
    }


    /**
     * @param Member $member
     *
     * @return $resetPasswordRequest
     */
    public function saveResetPasswordRequest($member)
    {
        $uniqueHash = md5(time().$member->getEmail());
        $expires = new \DateTime('now');
        $expires->add(new \DateInterval('P1D'));

        $resetPasswordRequest = new ResetPasswordRequest();
        $resetPasswordRequest->setUniqueHash($uniqueHash);
        $resetPasswordRequest->setExpires($expires);
        $resetPasswordRequest->setEmail($member->getEmail());

        $resetPasswordRequest = $this->saveEntity($resetPasswordRequest);

        return $resetPasswordRequest;        
    }

    /**
     * @param string $uniqueHash
     *
     * @return boolean
     */
    public function getResetPasswordRequestByUniqueHash($uniqueHash)
    {
        $resetPasswordRequest = $this->doctrine
                                     ->getEntityManager()
                                     ->getRepository('GrabagameBookingBundle:ResetPasswordRequest')
                                     ->findOneByUniqueHash($uniqueHash);

        return $resetPasswordRequest;
    }

    /**
     * @param string $uniqueHash
     *
     * @return boolean
     */
    public function isHashValid($uniqueHash)
    {
        $resetPasswordRequest = $this->doctrine
                                     ->getEntityManager()
                                     ->getRepository('GrabagameBookingBundle:ResetPasswordRequest')
                                     ->findOneByUniqueHash($uniqueHash);

        if ($resetPasswordRequest->getExpires() < new \DateTime('now')) {
            $resetPasswordRequest = false;
        }

        return ($resetPasswordRequest) ? true : false;
    }

    /**
     * @param string $uniqueHash
     */
    public function setHashExpired($uniqueHash)
    {
        $resetPasswordRequest = $this->getResetPasswordRequestByUniqueHash($uniqueHash);
        $resetPasswordRequest->setExpires(new \DateTime('now'));
        $this->saveEntity($resetPasswordRequest);
    }

    /**
     * @param string $uniqueHash
     *
     * @return string
     */
    public function getEmailFromUniqueHash($uniqueHash)
    {
        $passwordResetRequest = $this->doctrine
                                     ->getEntityManager()
                                     ->getRepository('GrabagameBookingBundle:ResetPasswordRequest')
                                     ->findOneByUniqueHash($uniqueHash);

        return $passwordResetRequest->getEmail();
    }  
    
}
