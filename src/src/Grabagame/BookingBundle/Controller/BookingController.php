<?php
namespace Grabagame\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Grabagame\BookingBundle\Form\Type\BookingType,
    Grabagame\BookingBundle\Entity\Booking,
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
        try {
            $clubService = $this->get('service.club');
            $bookingService = $this->get('service.booking');

            $dayToDisplay = $bookingService->getDayToDisplay($dayToDisplay);
            $yesterday = $bookingService->getYesterday($dayToDisplay);
            $tomorrow = $bookingService->getTomorrow($dayToDisplay);

            $club = $clubService->getClubById('1');
            $startTimes = $clubService->getStartTimes($club, $dayToDisplay);

            $bookingCollection = $bookingService->getBookingsByDate($club, $dayToDisplay);

            $bindings = array(
                'Club'              => $club,
                'StartTimes'        => $startTimes,
                'BookingCollection' => $bookingCollection,
                'DayToDisplay'      => $dayToDisplay,
                'Tomorrow'          => $tomorrow,
                'Yesterday'         => $yesterday,
            );

            return $this->render('GrabagameBookingBundle:Booking:renderBookingTable.html.twig', $bindings);
        } catch (BookingException $e) {

            return $this->renderBookingException($e);
        } catch (\Exception $e) {

            return $this->renderException($e);
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
        } catch (BookingException $e) {

            return $this->renderBookingException($e);
        } catch (\Exception $e) {

            return $this->renderException($e);
        }
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
        try {
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

            $dayToDisplay = $booking->getStartTime()->format('Y-m-d G:i');
            $bindings = array('DayToDisplay' => $dayToDisplay);

            return $this->forward('GrabagameBookingBundle:Booking:renderBookingTable', $bindings);
        } catch (BookingException $e) {

            return $this->renderBookingException($e);
        } catch (\Exception $e) {

            return $this->renderException($e);
        }
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
        $this->get('session')->setFlash('notice', $flashMessage);
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
        $this->get('session')->setFlash('notice', $flashMessage);
    }

    /**
     * @param integer $bookingId 
     *
     * @return Response
     */
    public function cancelAction($bookingId)
    {
        try {
            $bookingService = $this->get('service.booking');
            $booking = $bookingService->getBookingById($bookingId);
            
            $bindings = array(
                'Booking' => $booking,
                'Club' => $booking->getClub(),
            );

            return $this->render('GrabagameBookingBundle:Booking:confirmCancelBooking.html.twig', $bindings);

        } catch (BookingException $e) {

            return $this->renderBookingException($e);
        } catch (\Exception $e) {

            return $this->renderException($e);
        }
    }

    /**
     * @param integer $bookingId
     *
     * @return Response
     */
    public function submitCancelAction($bookingId)
    {
        try {
            $bookingService = $this->get('service.booking');
            $booking = $bookingService->getBookingById($bookingId);

            $bookingService->cancelBooking($booking);

            $this->get('session')->setFlash('notice', 'Your booking has been successfully cancelled');

            return $this->redirect($this->generateUrl('booking'));
        } catch (BookingException $e) {

            return $this->renderBookingException($e);
        } catch (\Exception $e) {

            return $this->renderException($e);
        }
    }

    /**
     * @param BookingException $e
     *
     * @return Response
     */
    private function renderBookingException($e)
    {
        $logger = $this->get('logger');
        $logger->err($e);

        $bindings = array(
            'ErrorMessage' => $e->getMessage(),
        );

        return $this->render('GrabagameBookingBundle::exception.html.twig', $bindings);
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

        return $this->render('GrabagameBookingBundle::exception.html.twig');
    }
}