<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Ranking;
use App\Form\NewPasswordType;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", priority=10, name="register")
     */
    public function register(Request $request,
                            EntityManagerInterface $manager,
                            UserPasswordEncoderInterface $encoder) {
        $user = new User();
        $rank = $this->getDoctrine()->getRepository(Ranking::class)->find(1);

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setRank($rank);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('login');
        }
        return $this->render('logs/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", priority=10, name="login")
     */
    public function login() {        
        return $this->render('logs/login.html.twig');
    }

    /**
     * @Route("/reset-password", priority=10, name="reset_password")
     */
    public function reset(Request $request, \Swift_Mailer $mailer) {

        $message = (new \Swift_Message('Mail de récupération'))
            ->setFrom('alexandre.meddas@outlook.fr')
            ->setTo($request->get('email'))
            ->setBody(
                $this->renderView('emails/reset.html.twig',
                ['email' => $request->get('email')]),
                'text/html'
            )
        ;
        $mailer->send($message);

        $this->addFlash('message', 'Un mail vous à été envoyé !');
        
        return $this->render('logs/reset.html.twig');
    }
    /**
     * @Route("/new-password/{email}", priority=10, name="update_password")
     */
    public function updatePassword($email,
                                   Request $request,
                                   EntityManagerInterface $manager,
                                   UserPasswordEncoderInterface $encoder) {

        $userDB = $this->getDoctrine()->getRepository(User::class)->findOneByEmail($email);

        $form = $this->createForm(NewPasswordType::class, $userDB);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($userDB, $userDB->getPassword());

            $userDB->setPassword($hash);

            $manager->persist($userDB);
            $manager->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('logs/newPassword.html.twig',
        [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", priority=10, name="logout")
     */
    public function logout() {}
}
