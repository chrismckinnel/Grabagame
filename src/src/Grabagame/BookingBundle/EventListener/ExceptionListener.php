<?php
namespace Grabagame\BookingBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent,
    Symfony\Component\HttpFoundation\Response,
    Grabagame\BookingBundle\Exception\BookingException,
    Grabagame\BookingBundle\Service\LoggerAware;

class ExceptionListener extends LoggerAware
{
    protected $templating;
    protected $logger;

    /**
     * @param Templating $templating
     */
    public function setTemplating($templating)
    {
        $this->templating = $templating;
    }

    /**
     * 
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $this->logException($exception);

        if ($exception instanceOf BookingException) {
            $bindings = array(
                'ExceptionMessage' => $exception->getMessage()
            );
        }

        if ($exception->getCode() == 404) {
            $bindings = array(
                'Exception' => $exception,
            );

            $response = new Response($this->templating->render('GrabagameBookingBundle:Exception:not-found.html.twig', $bindings));
            $event->setResponse($response);
        } else {
            $response = new Response($this->templating->render('GrabagameBookingBundle:Exception:exception.html.twig'));

            $event->setResponse($response);
        }
    }

    /**
     * @param Exception $e
     */
    private function logException($e)
    {
        $this->logger->err($e->getMessage());
        $this->logger->info($e->getTraceAsString());
    }    
}
