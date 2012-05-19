<?php
namespace Grabagame\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Controller for member pages
 *
 * @package Grabagame\BookingBundle\Controller
 * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
 */

class MemberController extends Controller
{
    /**
     * @return Response
     */
    public function forgotPasswordAction()
    {
        return $this->render('GrabagameBookingBundle:ResetPassword:forgotPasswordForm.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function resetPasswordRequestAction(Request $request)
    {
        try {
            $bindings = array();
            $email = $request->request->get('email');
            $memberService = $this->get('service.member');

            $member = $memberService->getMemberByEmail($email);

            $resetPasswordRequest = $memberService->saveResetPasswordRequest($member);
            $this->sendResetPasswordRequest($member, $resetPasswordRequest);

            $bindings = array('Member' => $member);

        } catch (MembersnetException $e) {
            $memberService->logError($e->getMessage());
            $bindings = array('Error' => $e->getMessage());

        } catch (\Exception $e) {
            $logger = $this->get('logger');
            $logger->err($e->getMessage());
            $bindings = array('Error' => 'Something went wrong during your password reset request, this error has been flagged so hopefully will be resolved soon.');

        }

        return $this->render('GrabagameBookingBundle:ResetPassword:resetPasswordRequest.html.twig', $bindings);
    }

    /**
     * @param Member               $member                     Member object
     * @param ResetPasswordRequset $resetPasswordRequest Reset password request object
     *
     * @return integer
     */
    private function sendResetPasswordRequest($member, $resetPasswordRequest)
    {
        $bindings = array(
            'Member' => $member,
            'ResetPasswordRequest' => $resetPasswordRequest,
        );

        $mailer = $this->get('mailer');

        $body = $this->renderView('GrabagameBookingBundle:Email:resetPasswordRequest.html.twig', $bindings);
        $message = \Swift_Message::newInstance()
            ->setContentType('text/html')
            ->setSubject('Password reset request')
            ->setFrom('no-reply@grabagame.co.nz')
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

            $memberService->setHashExpired($uniqueHash);

            $bindings = array('UniqueHash' => $uniqueHash);

            return $this->render('GrabagameBookingBundle:ResetPassword:resetPassword.html.twig', $bindings);
        } else {
            $bindings = array('Error' => 'Something went wrong with your activation link, maybe it has expired. You can make another request below.');

            return $this->render('GrabagameBookingBundle:ResetPassword:forgotPasswordForm.html.twig', $bindings);
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
            $memberService = $this->get('service.member');
            $userManager = $this->get('fos_user.user_manager');

            $password = $request->request->get('password');
            $email = $memberService->getEmailFromUniqueHash($uniqueHash);

            $member = $memberService->getMemberByEmail($email);
            $member->setPlainPassword($password);
            $userManager->updateUser($member);

            $bindings = array('FirstName' => $member->getFirstName());
            $flashMessage = $this->renderView('GrabagameBookingBundle:ResetPassword:passwordResetSuccess.html.twig', $bindings);
            $this->get('session')->setFlash('alert-success', $flashMessage);

        } catch (MemberException $e) {
            $memberService->logError('Resetting a password failed');
            $memberService->logError($e->getMessage());

            $bindings = array('Error' => $e->getMessage());
            $flashMessage = $this->renderView('GrabagameBookingBundle:ResetPassword:passwordResetFailed.html.twig', $bindings);
            $this->get('session')->setFlash('alert-error', $flashMessage);

        } catch (\Exception $e) {
            $logger = $this->get('logger');
            $logger->err($e->getMessage());

            $flashMessage = 'Something went wrong during your password reset request, a developer has been notified and we hope to have this resolved shortly.';
            $this->get('session')->setFlash('alert-error', $flashMessage);
        }

        return $this->redirect($this->generateUrl('fos_user_security_login'));

    }    
    

}
