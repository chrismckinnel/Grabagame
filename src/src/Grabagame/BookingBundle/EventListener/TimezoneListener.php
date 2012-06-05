<?php
namespace Grabagame\BookingBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent,
    Symfony\Component\HttpFoundation\Response,
    Grabagame\BookingBundle\Exception\BookingException,
    Grabagame\BookingBundle\Service\LoggerAware;

class TimezoneListener extends LoggerAware
{
    private $securityContext;
    private $connection;

    /**
     * @param SecurityContext $securityContext
     */
    public function setSecurityContext($securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @param Connection $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Kernel request listener
     */
    public function onKernelRequest()
    {
        if (!$this->securityContext->isGranted('ROLE_USER')) {
            return;
        }

        $club = $this->securityContext
                     ->getToken()
                     ->getUser()
                     ->getClub();

        if (!$club->getTimezone()) {
            return;
        }

        date_default_timezone_set($club->getTimezone());
        $this->connection->query("SET time_zone = '{$club->getTimezone()}'");
    }
}
