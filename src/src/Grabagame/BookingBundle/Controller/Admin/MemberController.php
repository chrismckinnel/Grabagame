<?php

namespace Grabagame\BookingBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request, 
    Grabagame\BookingBundle\Form\Type\MemberType,
    Grabagame\BookingBundle\Entity\Group,
    Grabagame\BookingBundle\Entity\Member;

/**
 * Controller for member management
 *
 * @package GrabagameBookingBundle
 * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
 */
class MemberController extends Controller
{

    /**
     * List action
     *
     * @return Response
     */
    public function listAction()
    {
        $memberService = $this->get('service.member');
        $members = $memberService->getAllMembers();

        $searchForm = $this->createFormBuilder()
                     ->add('search_member', 'text')
                     ->getForm();

        $bindings = array('Members' => $members,
                          'member_search_form' => $searchForm->createView());

        return $this->render('GrabagameBookingBundle:Admin:Member/searchResults.html.twig', $bindings);
    }

    /**
     * @param integer $memberId Member ID
     *
     * @return Response
     */
    public function detailsAction($memberId)
    {
        $memberService = $this->get('service.member');
        $member = $memberService->getMemberById($memberId);

        $bindings = array('Member' => $member);

        return $this->render('GrabagameBookingBundle:Admin:Member/view.html.twig', $bindings);
    }

    /**
     * @param integer $memberId Member ID
     *
     * @return Response
     */
    public function editAction($memberId)
    {

        $memberService = $this->get('service.member');
        $groupService = $this->get('service.group');

        $member = $memberService->getMemberById($memberId);
        $form = $this->createForm(new MemberType(), $member);

        $bindings = array('Member' => $member,
                          'member_form' => $form->createView());

        return $this->render('GrabagameBookingBundle:Admin:Member/edit.html.twig', $bindings);
    }

    /**
     * @param Request $request Request object
     *
     * @return Response
     */
    public function submitEditAction(Request $request)
    {
        try {
            $memberArray = $request->request->get('member');
            $memberId = $memberArray['id'];

            $memberService = $this->get('service.member');
            $member = $memberService->getMemberById($memberId);

            $form = $this->createForm(new MemberType(), $member);

            if ($request->getMethod() == 'POST') {
                $form->bindRequest($request);

                if ($form->isValid()) {
                    $entityManager = $this->getDoctrine()
                                          ->getEntityManager();
                    $entityManager->persist($member);
                    $entityManager->flush();

                    $this->get('session')->setFlash('alert-success', 'Successfully edited the member '.$member->getFirstName().' '.$member->getLastName());
                } else {
                    $this->get('session')->setFlash('alert-error', 'Some errors occurred: '.$form->getErrors());
                }
            }

        } catch (\Exception $e) {
            $logger = $this->get('logger');
            $logger->err($e->getMessage());
            $this->get('session')->setFlash('alert-error', 'Something went wrong during your password reset request, a developer has been notified and we hope to have this resolved shortly.');
        }

        return $this->redirect($this->generateUrl('member_search'));
    }

    /**
     * @param Member $member Member object
     *
     * @return boolean
     */
    public function memberExists($member)
    {
        $memberRepository = $this->getDoctrine()
                                 ->getRepository('GrabagameBookingBundle:Member');

        $member = $memberRepository->findOneByMemberNumber($member->getMemberNumber());

        return ($member) ? true : false;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function searchAction(Request $request)
    {
        $query = $request->query->get('q');
        $memberService = $this->get('service.member');
        $members = $memberService->searchMembers($query);

        $bindings = array(
            'Members' => $members,
            'q' => $query,
        );
        $format = $request->getRequestFormat();

        return $this->render('GrabagameBookingBundle:Admin:Member/searchResults.'.$format.'.twig', $bindings);
    }

    /**
     * @return Response
     */
    public function forgotPasswordAction()
    {
        return $this->render('GrabagameBookingBundle:Member:forgotPasswordForm.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function resetPasswordRequestAction(Request $request)
    {
        $bindings = array();
        try {
            $email = $request->request->get('email');
            $memberService       = $this->get('service.member');
            $membersnetService = $this->get('service.membersnet');
            $mailerService     = $this->get('mailer');

            $memberArray = $membersnetService->getMemberDetails($email, 'Email');
            $member = new Member();
            $member = $membersnetService->populateMemberObjectFromArray($member, $memberArray);
            $resetPasswordRequest = $memberService->saveResetPasswordRequest($member);
            $this->sendResetPasswordRequest($member, $resetPasswordRequest);

            $bindings = array('Member' => $member);

        } catch (MembersnetException $e) {
            $membersnetService->logError('Importing a member failed.');
            $membersnetService->logError($e->getMessage());
            $bindings = array('Error' => $e->getMessage());

        } catch (\Exception $e) {
            $logger = $this->get('logger');
            $logger->err($e->getMessage());
            $bindings = array('Error' => 'Something went wrong during your password reset request, a developer has been notified and we hope to have this resolved shortly.');

        }

        return $this->render('GrabagameBookingBundle:Member:resetPasswordRequest.html.twig', $bindings);
    }

    /**
     * @param Member                 $member                 Member object
     * @param ResetPasswordRequset $resetPasswordRequest Reset password request object
     *
     * @return integer
     */
    private function sendResetPasswordRequest($member, $resetPasswordRequest)
    {
        $mailer = $this->get('mailer');

        $bindings = array(
            'Member' => $member,
            'ResetPasswordRequest' => $resetPasswordRequest,
        );

        $body = $this->renderView('GrabagameBookingBundle:Member:Email/resetPasswordRequest.html.twig', $bindings);
        $message = \Swift_Message::newInstance()
            ->setContentType('text/html')
            ->setSubject('Password reset request')
            ->setFrom('no-reply@labour.org.uk')
            ->setTo($member->getEmail())
            ->setBody($body);

        return $mailer->send($message);
    }

    /**
     * @param string $uniqueHash
     *
     * @return Response
     */
    public function resetPasswordFormAction($uniqueHash)
    {
        $memberService = $this->get('service.member');

        if ($memberService->isHashValid($uniqueHash)) {
            //todo set hash to expired
            $bindings = array('UniqueHash' => $uniqueHash);

            return $this->render('GrabagameBookingBundle:Member:resetPassword.html.twig', $bindings);
        } else {
            $bindings = array('Error' => 'Something went wrong with your activation link, maybe it has expired. You can make another request below.');

            return $this->render('GrabagameBookingBundle:Member:forgotPasswordForm.html.twig', $bindings);
        }
    }

    /**
     * @param string  $uniqueHash Unique hash
     * @param Request $request    Request object
     *
     * @return Response
     */
    public function submitResetPasswordAction($uniqueHash, Request $request)
    {
        try {
            $password = $request->request->get('password');
            $memberService = $this->get('service.member');
            $email = $memberService->getEmailFromUniqueHash($uniqueHash);

            $membersnetService = $this->get('service.membersnet');
            $resetResult = $membersnetService->resetPassword($email, $password);

            $bindings = array('FirstName' => $resetResult['FirstName']);
            $flashMessage = $this->renderView('GrabagameBookingBundle:Member:passwordResetSuccess.html.twig', $bindings);
            $this->get('session')->setFlash('alert-success', $flashMessage);

        } catch (MembersnetException $e) {
            $membersnetService->logError('Resetting a password failed');
            $membersnetService->logError($e->getMessage());

            $bindings = array('Error' => $e->getMessage());
            $flashMessage = $this->renderView('GrabagameBookingBundle:Member:passwordResetFailed.html.twig', $bindings);
            $this->get('session')->setFlash('alert-error', $flashMessage);

        } catch (\Exception $e) {
            $logger = $this->get('logger');
            $logger->err($e->getMessage());

            $flashMessage = 'Something went wrong during your password reset request, a developer has been notified and we hope to have this resolved shortly.';
            $this->get('session')->setFlash('alert-error', $flashMessage);
        }

        return $this->redirect($this->generateUrl('fos_member_security_login'));

    }
}
