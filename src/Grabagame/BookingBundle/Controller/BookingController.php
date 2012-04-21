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
        try {
            $clubService = $this->get('service.club');
            $bookingService = $this->get('service.booking');

            $club = $clubService->getClubById('3');
            $startTimes = $clubService->getStartTimes($club);
            $bookingCollection = $bookingService->getBookingsByDate($club, new \DateTime('2012-01-01'));

            $bindings = array(
                'Club' => $club,
                'StartTimes' => $startTimes,
                'BookingCollection' => $bookingCollection,
            );

            return $this->render('GrabagameBookingBundle:Booking:renderBookingTable.html.twig', $bindings);
        } catch (\Exception $e) {
            $logger = $this->get('logger');
            $logger->err($e);
            
            return $this->render('GrabagameBookingBundle::exception.html.twig');
        }
    }

    /**
     * @param Booking  $booking  Current booking
     * @param DateTime $slotTime Time of slot
     *
     * @return Response
     */
    public function renderBookingSlotAction($booking, $slotTime)
    {
        $bindings = array(
            'Booking' => $booking,
            'SlotTime' => $slotTime,
        );

        return $this->render('GrabagameBookingBundle:Booking:renderBookingSlot.html.twig', $bindings);
    }
}
