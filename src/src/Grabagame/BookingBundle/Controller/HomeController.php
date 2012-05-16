<?php
namespace Grabagame\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Controller for general pages
 *
 * @package Grabagame\BookingBundle\Controller
 * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
 */
class HomeController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('GrabagameBookingBundle:Home:index.html.twig');
    }

    /**
     * @return Response
     */
    public function aboutAction()
    {
        try {
            return $this->render('GrabagameBookingBundle:Home:about.html.twig');
        } catch (\Exception $e) {
            return $this->renderException($e);
        }
    }

    /**
     * @return Response
     */
    public function signInFormAction()
    {
        $csrfToken = $this->get('form.csrf_provider')->generateCsrfToken('authenticate');

        $bindings = array(
            'csrfToken' => $csrfToken,
        );

        return $this->render('GrabagameBookingBundle:Home:signInForm.html.twig', $bindings);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function submitErrorAction(Request $request)
    {
        $errorReport = $request->get('errorReport');
        $mailer = $this->get('mailer');

        $bindings = array(
            'ErrorReport' => $errorReport,
        );

        $body = $this->renderView('GrabagameBookingBundle:Email:errorReport.html.twig', $bindings);
        $message = \Swift_Message::newInstance()
            ->setContentType('text/html')
            ->setSubject('User error report')
            ->setFrom('support@grabagame.co.nz')
            ->setTo('chrismckinnel@gmail.com')
            ->setBody($body);

        $mailer->send($message);

        $this->get('session')->setFlash('alert-info', 'Thanks, hopefully we\'ll have the issue fixed shortly');

        return $this->redirect($this->generateUrl('bookingDefault'));
    }

     /**
     * @param Exception $e
     *
     * @return Response
     */
    private function renderException($e)
    {
        $logger = $this->get('logger');
        $logger->err($e->getMessage());

        return $this->render('GrabagameBookingBundle:Exception:exception.html.twig');
    }   
}
