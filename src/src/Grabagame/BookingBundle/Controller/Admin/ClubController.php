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
    public function listAction()
    {
        $memberService = $this->get('service.member');
        $clubService = $this->get('service.club');

        $clubs = $clubService->getAllClubs();
        $club = $memberService->getLoggedInMember()
                              ->getClub();

        $bindings = array(
            'Clubs' => $clubs,
            'Club' => $club,
        );

        return $this->render('GrabagameBookingBundle:Admin:Club/list.html.twig', $bindings);
    }

    /**
     * @param integer $clubId
     *
     * @return Response
     */
    public function detailsAction($clubId)
    {
        $clubService = $this->get('service.club');
        $club = $clubService->getClubById($clubId);

        $bindings = array(
            'Club' => $club,
        );

        return $this->render('GrabagameBookingBundle:Admin:Club/details.html.twig', $bindings);
    }

    /**
     * @param integer $clubId
     *
     * @return Response
     */
    public function editAction($clubId)
    {
        $clubService = $this->get('service.club');
        $club = $clubService->getClubById($clubId);

        $form = $this->createForm(new ClubType(), $club);

        $bindings = array('Club' => $club,
                          'club_form' => $form->createView());

        return $this->render('GrabagameBookingBundle:Admin:Club/edit.html.twig', $bindings);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function submitEditAction(Request $request)
    {
        $clubArray = $request->request->get('club');
        $numberOfCourts = $request->get('numberOfCourts');
        $clubId = $clubArray['id'];

        $clubService = $this->get('service.club');
        $club = $clubService->getClubById($clubId);

        $form = $this->createForm(new ClubType(), $club);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $clubService->updateCourts($club, $numberOfCourts);
                $clubService->saveClub($club);

                $bindings = array('Club' => $club);
                $flashMessage = $this->renderView('GrabagameBookingBundle:Admin:Club/editSuccessful.html.twig', $bindings);

                $this->get('session')->setFlash('alert-success', $flashMessage);
            } else {
                $errors = $form->getErrors();
                $bindings = array('Errors' => $errors);

                return $this->render('GrabagameBookingBundle:Admin:Club/editFailed.html.twig', $bindings);
            }
        }

        return $this->redirect($this->generateUrl('club_list'));
    }    

    /**
     * @return Response
     */
    public function addClubAction()
    {
        $club = new Club();
        $clubForm = $this->createForm(new ClubType(), $club);
        $bindings = array('club_form' => $clubForm->createView());

        return $this->render('GrabagameBookingBundle:Admin:Club/add.html.twig', $bindings);
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
                $flashMessage = $this->renderView('GrabagameBookingBundle:Admin:Club/addSuccessful.html.twig', $bindings);

                $this->get('session')->setFlash('noticeSuccess', $flashMessage);
            } else {
                $errors = $clubForm->getErrors();
                $bindings = array('Errors' => $errors);

                return $this->render('GrabagameBookingBundle:Admin:Club/addFailed.html.twig', $bindings);
            }
        }

        return $this->redirect($this->generateUrl('add_club'));
    }

    /**
     * @param integer $clubId
     *
     * @return Response
     */
    public function activateAction($clubId)
    {
        $clubService = $this->get('service.club');
        $club = $clubService->activate($clubId);

        if ($club) {
            $this->get('session')->setFlash('alert-success', 'Successfully activated '.$club->getName().'.');
        } else {
            $this->get('session')->setFlash('alert-error', 'Activation failed for club with ID '.$clubId);
        }

        return $this->redirect($this->generateUrl('club_list'));
    }

    /**
     * @param integer $clubId
     *
     * @return Response
     */
    public function deactivateAction($clubId)
    {
        $clubService = $this->get('service.club');
        $club = $clubService->deactivate($clubId);

        if ($club) {
            $this->get('session')->setFlash('alert-success', 'Successfully deactivated '.$club->getName().'.');
        } else {
            $this->get('session')->setFlash('alert-errr', 'Deactivation failed for club with ID '.$clubId);
        }

        return $this->redirect($this->generateUrl('club_list'));
    }    
}
