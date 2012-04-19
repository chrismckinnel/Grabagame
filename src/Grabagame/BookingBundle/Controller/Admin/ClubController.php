<?php
namespace Grabagame\BookingBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Grabagame\BookingBundle\Entity\Club,
    Grabagame\BookingBundle\Form\Type\ClubType;

/**
 * Controller for club management
 *
 * @package Grabagame\BookingBundle\Admin\Controller
 * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
 */
class ClubController extends Controller
{

    /**
     * @return Response
     */
    public function addClubAction()
    {
        $club = new Club();
        $clubForm = $this->createForm(new ClubType(), $club);
        $bindings = array('club_form' => $clubForm->createView());

        return $this->render('GrabagameBookingBundle:Admin:addClub.html.twig', $bindings);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function submitAddClubAction(Request $request)
    {
        $club = new Club();
        $clubForm = $this->createForm(new ClubType(), $club);
        $clubService = $this->get('service.club');

        if ($request->getMethod() == 'POST') {
            $clubForm->bindRequest($request);

            if ($clubForm->isValid()) {
                $clubService->saveClub($club);

                $bindings = array('Club' => $club);
                $flashMessage = $this->renderView('GrabagameBookingBundle:Admin:addClubSuccessful.html.twig', $bindings);

                $this->get('session')->setFlash('noticeSuccess', $flashMessage);
            } else {
                $errors = $clubForm->getErrors();
                $bindings = array('Errors' => $errors);

                return $this->render('GrabagameBookingBundle:Admin:addClubFailed.html.twig', $bindings);
            }
        }

        return $this->redirect($this->generateUrl('add_club'));
    }
}
