<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ResetCode;
use AppBundle\Entity\User;
use AppBundle\Form\ForgotPasswordType;
use AppBundle\Form\LoginForm;
use AppBundle\Form\ResetPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginForm::class, [
            '_username' => $lastUsername
        ]);

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {

    }

    /**
     * @Route("/forgot-password", name="password_forgot")
     */
    public function forgotAction(Request $request)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $user = $this->getDoctrine()->
                getRepository(User::class)->
                findOneByEmail($data['email']);
            if (!$user) {
                // for security...
                $this->addFlash('success', 'Alright... We\'ve sent you an email you can use to reset your password.');

                return $this->redirectToRoute('homepage');
            }

            $resetCode = new ResetCode();
            // generate code
            $code = md5($user->getEmail() . time() . strrev($user->getEmail()));
            // store code
            $resetCode->setCode($code);
            $resetCode->setUserId($user->getId());

            $em = $this->getDoctrine()->getManager();
            $em->persist($resetCode);
            $em->flush();
            // send email

            $url = $this->generateUrl(
                'password_reset',
                ['code' => $code],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $message = \Swift_Message::newInstance()
                ->setSubject('Reset Your Password')
                ->setFrom(['joshua.milford@gmail.com' => 'Insulin Calculator'])
                ->setTo($user->getEmail())
                ->setBody(
                    '<p>Please visit <a href="'.$url.'">'.$url.'</a> to reset your password.</p>'
                    ,'text/html'
                );

            $this->get('mailer')->send($message);

            // redirect
            $this->addFlash('success', 'Alright... We\'ve sent you an email you can use to reset your password.');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('security/forgot.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset-password/{code}", name="password_reset")
     * @param ResetCode $resetCode
     * @param $request
     * @return Response
     */
    public function resetAction(Request $request, ResetCode $resetCode)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($resetCode->getUserId());

        if($resetCode->getIsUsed() || !$user) {
            $this->addFlash('error', 'We could not find that reset code');

            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isValid()) {

            $resetCode->setIsUsed(1);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Your password has been reset');

            return $this->redirectToRoute('login');
        }

        return $this->render('security/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
