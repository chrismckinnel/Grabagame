<?php

namespace Grabagame\BookingBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Grabagame\BookingBundle\Entity\Member,
    Grabagame\BookingBundle\Entity\Group,
    Grabagame\BookingBundle\Form\Type\GroupType;

/**
 * Controller for groups
 *
 * @package GrabagameBookingBundle
 * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
 */
class GroupController extends Controller
{

    /**
     * @return Response
     */
    public function listAction()
    {
        $groupService = $this->get('service.group');
        $groups = $groupService->getAllGroups();

        foreach ($groups as $group) {
            $group->setNumberOfMembers($groupService->getNumberOfMembers($group));
        }

        $bindings = array('Groups' => $groups);

        return $this->render('GrabagameBookingBundle:Admin:Group/list.html.twig', $bindings);
    }

    /**
     * @param integer $groupId
     *
     * @return Response
     */
    public function detailsAction($groupId)
    {
        $groupService = $this->get('service.group');
        $group = $groupService->getGroupById($groupId);

        $numberOfMembers = $groupService->getNumberOfMembers($group);

        $bindings = array('Group' => $group,
                          'NumberOfMembers' => $numberOfMembers);

        return $this->render('GrabagameBookingBundle:Admin:Group/details.html.twig', $bindings);
    }

    /**
     * @param integer $groupId
     *
     * @return Response
     */
    public function editAction($groupId)
    {
        $groupService = $this->get('service.group');
        $group = $groupService->getGroupById($groupId);

        $form = $this->createForm(new GroupType(), $group);

        $bindings = array('Group' => $group,
                          'group_form' => $form->createView());

        return $this->render('GrabagameBookingBundle:Admin:Group/edit.html.twig', $bindings);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function submitEditAction(Request $request)
    {
        $groupArray = $request->request->get('group');
        $groupId = $groupArray['id'];

        $groupService = $this->get('service.group');
        $group = $groupService->getGroupById($groupId);

        $form = $this->createForm(new GroupType(), $group);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $groupService->saveGroup($group);

                $bindings = array('Group' => $group);
                $flashMessage = $this->renderView('GrabagameBookingBundle:Admin:Group/editSuccessful.html.twig', $bindings);

                $this->get('session')->setFlash('noticeSuccess', $flashMessage);
            } else {
                $errors = $form->getErrors();
                $bindings = array('Errors' => $errors);

                return $this->render('GrabagameBookingBundle:Admin:Group/editFailed.html.twig', $bindings);
            }
        }

        return $this->redirect($this->generateUrl('group_list'));
    }

    /**
     * @return Response
     */
    public function addAction()
    {
        $group = new Group();
        $form = $this->createForm(new GroupType(), $group);

        $bindings = array('group_form' => $form->createView());

        return $this->render('GrabagameBookingBundle:Admin:Group/add.html.twig', $bindings);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function submitAddAction(Request $request)
    {
        $group = new Group();
        $form = $this->createForm(new GroupType(), $group);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()
                                      ->getEntityManager();
                $entityManager->persist($group);
                $entityManager->flush();

                $bindings = array('Group' => $group);
                $flashMessage = $this->renderView('GrabagameBookingBundle:Admin:Group/addSuccessful.html.twig', $bindings);

                $this->get('session')->setFlash('noticeSuccess', $flashMessage);
            } else {
                $errors = $form->getErrors();
                $bindings = array('Errors' => $errors);

                return $this->render('GrabagameBookingBundle:Admin:Group/addFailed.html.twig', $bindings);
            }
        }

        return $this->redirect($this->generateUrl('group_list'));
    }

    /**
     * @param integer $groupId
     *
     * @return Response
     */
    public function activateAction($groupId)
    {
        $groupService = $this->get('service.group');
        $group = $groupService->activate($groupId);

        if ($group) {
            $this->get('session')->setFlash('noticeSuccess', 'Successfully activated '.$group->getName().'.');
        } else {
            $this->get('session')->setFlash('noticeFailed', 'Activation failed for group with ID '.$groupId);
        }

        return $this->redirect($this->generateUrl('group_list'));
    }

    /**
     * @param integer $groupId
     *
     * @return Response
     */
    public function deactivateAction($groupId)
    {
        $groupService = $this->get('service.group');
        $group = $groupService->deactivate($groupId);

        if ($group) {
            $this->get('session')->setFlash('noticeSuccess', 'Successfully deactivated '.$group->getName().'.');
        } else {
            $this->get('session')->setFlash('noticeFailed', 'Deactivation failed for group with ID '.$groupId);
        }

        return $this->redirect($this->generateUrl('group_list'));
    }
}
