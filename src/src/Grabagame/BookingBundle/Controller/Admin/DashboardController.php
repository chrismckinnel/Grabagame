<?php
namespace Grabagame\BookingBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

/**
 * Controller for dashboard management
 *
 * @package Grabagame\BookingBundle\Admin\Controller
 * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
 */
class DashboardController extends Controller
{

    /**
     * @return Response
     */
    public function dashboardAction()
    {
        return $this->render('GrabagameBookingBundle:Admin:dashboard.html.twig');
    }

}
