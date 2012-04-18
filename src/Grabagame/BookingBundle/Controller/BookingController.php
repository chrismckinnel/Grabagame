<?php
namespace Grabagame\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

/**
 * Controller for booking management
 *
 * @package Grabagame\BookingBundle\Controller
 * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
 */
class BookingController extends Controller
{
    /**
     * @param 
     * 
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('GrabagameBookingBundle::layout.html.twig');
    }

    /**
     * @return Response
     */
    public function renderBookingTableAction()
    {
        return $this->render('GrabagameBookingBundle:Booking:renderBookingTable.html.twig');
    }
}
