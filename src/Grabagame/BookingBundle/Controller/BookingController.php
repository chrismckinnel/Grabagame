<?php
namespace Grabagame\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Grabagame\BookingBundle\Form\Type\BookingType,
    Grabagame\BookingBundle\Entity\Booking,
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
     *
     * @Secure(roles="ROLE_USER")
     *
     * @return Response
     */
    public function makeBookingAction($courtNumber, $startTime)
    {
        try {
            $startTime = htmlentities($startTime);
            $newStartTime = new \DateTime("now");
            $startTime = new \DateTime($newStartTime->format('Y-m-d '.$startTime));

            $bookingService = $this->get('service.booking');
            $memberService  = $this->get('service.member');

            $member = $memberService->getLoggedInMember();
            $club = $member->getClub();
            $court = $club->getCourtByNumber($courtNumber);

            $booking = $bookingService->createBooking($court, $member, $startTime);
            $booking_form = $this->createForm(new BookingType(), $booking);

            $bindings = array(
                'booking_form' => $booking_form->createView(),
                'Booking'      => $booking,
                'Club'         => $club,
            );

            return $this->render('GrabagameBookingBundle:Booking:makeBooking.html.twig', $bindings);
        } catch (\Exception $e) {
            $logger = $this->get('logger');
            $logger->err($e);

            return $this->render('GrabagameBookingBundle::exception.html.twig');
        }
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function submitMakeBookingAction(Request $request)
    {
        try {
            if ($request->getMethod() == 'POST') {
                $bookingService = $this->get('service.booking');
                $memberService  = $this->get('service.member');
                $member = $memberService->getLoggedInMember();

                $booking = new Booking();
                $bookingForm = $this->createForm(new BookingType(), $booking);
                $bookingForm->bindRequest($request);

                var_dump($booking);
                die();

                if ($bookingForm->isValid()) {
                    $bookingService->saveBooking($booking);
                    $flashMessage = $this->renderView('GrabagameBookingBundle:Booking:bookingSuccessful.html.twig');

                    $this->get('app.session')->setFlash('notice', $flashMessage);
                } else {
                    $bindings = array(
                        'booking_form' => $bookingForm->createView(),
                        'Booking'      => $booking,
                        'Club'         => $booking->getClub(),
                    );

                    return $this->render('GrabagameBookingBundle:Booking:makeBooking.html.twig', $bindings);
                }
            }

            $bindings = array('today' => $booking->getStartTime());

            return $this->redirect($this->generateUrl('booking', $bindings));
        } catch (\Exception $e) {
            $logger = $this->get('logger');
            $logger->err($e);

            return $this->render('GrabagameBookingBundle::exception.html.twig');
        }
    }
}
