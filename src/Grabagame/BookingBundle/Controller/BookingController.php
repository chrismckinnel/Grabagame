<?php
namespace Grabagame\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Grabagame\BookingBundle\Form\Type\BookingType,
    JMS\SecurityExtraBundle\Annotation\Secure;

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
    public function renderBookingTableAction($today = null)
    {
        try {
            $clubService = $this->get('service.club');
            $bookingService = $this->get('service.booking');

            if ($today == null) {
                $today = new \DateTime("now");
            }

            $club = $clubService->getClubById('3');
            $startTimes = $clubService->getStartTimes($club, $today);

            $bookingCollection = $bookingService->getBookingsByDate($club, $today);

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
     * @param integer  $courtNumber
     * @param DateTime $startTime
     * @Secure(roles="ROLE_USER")
     *
     * @return Response
     */
    public function makeBookingAction($courtNumber, $startTime)
    {
            $startTime = htmlentities($startTime);
            $newStartTime = new \DateTime("now");
            $startTime = $newStartTime->format('Y-m-d '.$startTime);

            $bookingService = $this->get('service.booking');
            $memberService  = $this->get('service.member');

            $member = $memberService->getLoggedInMember();
            $club = $member->getClub();
            $court = $club->getCourtByNumber($courtNumber);

            $booking = $bookingService->createBooking($court, $member, $startTime);
            $booking_form = $this->createForm(new BookingType(), $booking);

            $bindings = array(
                'booking_form' => $booking_form->createView(),
                'booking'      => $booking,
            );
        
        return $this->render('GrabagameBookingBundle:Booking:makeBooking.html.twig', $bindings);
    }

}
