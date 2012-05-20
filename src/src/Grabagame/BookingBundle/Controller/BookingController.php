<?php
namespace Grabagame\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Grabagame\BookingBundle\Form\Type\BookingType,
    Grabagame\BookingBundle\Entity\Booking,
    Grabagame\BookingBundle\Entity\BookingOnBehalf,
    Grabagame\BookingBundle\Exception\BookingException,
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
     * @return Response
     */ 
    public function indexAction()
    {
        return $this->render('GrabagameBookingBundle::layout.html.twig');
    }

    /**
     * @param string $dayToDisplay
     *
     * @return Response
     */
    public function renderBookingTableAction($dayToDisplay = null)
    {
        $clubService = $this->get('service.club');
        $bookingService = $this->get('service.booking');
        $memberService = $this->get('service.member');

        $dayToDisplay = $bookingService->getDayToDisplay($dayToDisplay);
        $yesterday = $bookingService->getYesterday($dayToDisplay);
        $tomorrow = $bookingService->getTomorrow($dayToDisplay);

        $member = $memberService->getLoggedInMember();
        $club = $member->getClub();

        $startTimes = $clubService->getStartTimes($club, $dayToDisplay);

        $bookings = $bookingService->getBookingsByDate($club, $dayToDisplay);
        $bookedSlots = $bookingService->getBookedSlotsForBookings($bookings);

        $bindings = array(
            'Club'              => $club,
            'StartTimes'        => $startTimes,
            'BookingCollection' => $bookedSlots,
            'DayToDisplay'      => $dayToDisplay,
            'Tomorrow'          => $tomorrow,
            'Yesterday'         => $yesterday,
            'BookingService'    => $bookingService,
        );

        return $this->render('GrabagameBookingBundle:Booking:renderBookingTable.html.twig', $bindings);
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
        $startTime = htmlentities($startTime);
        $startTime = new \DateTime($startTime);

        $bookingService = $this->get('service.booking');
        $memberService  = $this->get('service.member');

        $member = $memberService->getLoggedInMember();
        $club = $member->getClub();
        $court = $club->getCourtByNumber($courtNumber);

        $booking = $bookingService->createBooking($court, $member, $startTime);
        $booking->setClub($club);
        $maxSlots = $bookingService->getMaxSlots($booking);
        $booking_form = $this->createForm(new BookingType(), $booking);

        $bindings = array(
            'booking_form' => $booking_form->createView(),
            'Booking'      => $booking,
            'Club'         => $club,
            'MaxSlots'     => $maxSlots,
        );

        return $this->render('GrabagameBookingBundle:Booking:makeBooking.html.twig', $bindings);
    }

    /**
     * @param Request $request
     *
     * @Secure(roles="ROLE_USER")
     *
     * @return Response
     */
    public function submitMakeBookingAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $bookingService = $this->get('service.booking');
            $memberService  = $this->get('service.member');
            $clubService    = $this->get('service.club');

            $member = $memberService->getLoggedInMember();
            $club = $member->getClub();

            $booking = new Booking();
            $bookingForm = $this->createForm(new BookingType(), $booking);
            $bookingForm->bindRequest($request);

            $startTime = new \DateTime($request->get('startTime'));
            $court = $club->getCourtByNumber($request->get('courtNumber'));
            if ($request->get('nameHidden')) {
                $booking->setNameHidden(1);
            }

            $booking->setStartTime($startTime);
            $booking->setCourt($court);
            $booking->setMember($member);
            $booking->setClub($member->getClub());

            if ($bookingForm->isValid()) {

                $slots = $request->get('duration');
                $booking->setSlots($slots);

                if ($bookingService->isSlotAvailable($booking) == true) {

                    $bookingService->saveBooking($booking);
                    $this->setBookingSuccessfulFlash($member, $booking);

                    $onBehalf = $request->get('onBehalf');
                    if ($member->hasRole('BOOK_ON_BEHALF') && $onBehalf) {
                        $booking->setType('onBehalf');

                        $firstName = $request->get('firstName');
                        $lastName = $request->get('lastName');

                        $bookingOnBehalf = new BookingOnBehalf();
                        $bookingOnBehalf->setFirstName($firstName);
                        $bookingOnBehalf->setLastName($lastName);
                        $bookingOnBehalf->setBooking($booking);
                        $bookingOnBehalf = $bookingService->saveBookingOnBehalf($bookingOnBehalf);

                        $bookingService->saveBooking($booking);
                    }
                } else {
                    
                    $this->setBookingConflictFlash($member, $booking, $club);

                    $bindings = array(
                        'booking_form' => $bookingForm->createView(),
                        'Booking'      => $booking,
                        'Club'         => $club,
                    );

                    return $this->render('GrabagameBookingBundle:Booking:makeBooking.html.twig', $bindings);
                }

            } else {
                $bindings = array(
                    'booking_form' => $bookingForm->createView(),
                    'Booking'      => $booking,
                    'Club'         => $club,
                );

                return $this->render('GrabagameBookingBundle:Booking:makeBooking.html.twig', $bindings);
            }
        }

        $dayToDisplay = $booking->getStartTime()->format('Y-m-d');
        $bindings = array('dayToDisplay' => $dayToDisplay);

        return $this->redirect($this->generateUrl('booking', $bindings));
    }

    /**
     * @param Member  $member 
     * @param Booking $booking
     */
    private function setBookingSuccessfulFlash($member, $booking)
    {
        $bindings = array(
            'Member' => $member,
            'Booking' => $booking,
        );

        $flashMessage = $this->renderView('GrabagameBookingBundle:Booking:bookingSuccessful.html.twig', $bindings);
        $this->get('session')->setFlash('alert-success', $flashMessage);
    }

    /**
     * @param Member  $member 
     * @param Booking $booking 
     * @param Club    $club 
     */
    private function setBookingConflictFlash($member, $booking, $club)
    {
        $bindings = array(
            'Member'  => $member,
            'Booking' => $booking,
            'Club'    => $club,
        );

        $flashMessage = $this->renderView('GrabagameBookingBundle:Booking:slotNotAvailable.html.twig', $bindings);
        $this->get('session')->setFlash('alert-error', $flashMessage);
    }

    /**
     * @param integer $bookingId 
     *
     * @return Response
     */
    public function cancelAction($bookingId)
    {
        $bookingService = $this->get('service.booking');
        $booking = $bookingService->getBookingById($bookingId);
        
        $bindings = array(
            'Booking' => $booking,
            'Club' => $booking->getClub(),
        );

        return $this->render('GrabagameBookingBundle:Booking:confirmCancelBooking.html.twig', $bindings);
    }

    /**
     * @param integer $bookingId
     *
     * @return Response
     */
    public function submitCancelAction($bookingId)
    {
        $bookingService = $this->get('service.booking');
        $memberService = $this->get('service.member');
        $booking = $bookingService->getBookingById($bookingId);

        $member = $memberService->getLoggedInMember();
        $flashMessage = '';

        if ($booking->getMember() != $member) {
            $flashMessage = 'You have successfully cancelled '.$booking->getMember()->getFullName().'\'s booking for '.$booking->getStartTime()->format("l d F"). ' at '.$booking->getStartTime()->format('G:i a').'. He has been sent a notification email.';
        } else {
            $flashMessage = 'Your booking has been successfully cancelled';
        }
        
        $bookingService->cancelBooking($booking, $member);
        $this->get('session')->setFlash('alert-success', $flashMessage);

        return $this->redirect($this->generateUrl('bookingDefault'));
    }
}
