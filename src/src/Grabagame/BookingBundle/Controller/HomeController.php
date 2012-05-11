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
        return $this->render('GrabagameBookingBundle:Home:about.html.twig');
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
}
