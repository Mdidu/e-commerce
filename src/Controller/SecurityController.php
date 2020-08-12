<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Ranking;
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
     * @Route("/logout", priority=10, name="logout")
     */
    public function logout() {}
}
