<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Ranking;
use App\Form\LoginType;
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
        
        $usernameDB = $this->getDoctrine()->getRepository(User::class)->findOneByUsername($user->getUsername());
        $emailDB = $this->getDoctrine()->getRepository(User::class)->findOneByEmail($user->getEmail());
        
        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setRank($rank);

            if($usernameDB === NULL && $emailDB === NULL) {
                $manager->persist($user);
                $manager->flush();

                return $this->redirectToRoute('login');
            }
        }
        return $this->render('logs/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", priority=10, name="login")
     */
    public function login() {
        $user = new User();
        $form = $this->createForm(LoginType::class, $user);
        
        return $this->render('logs/login.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
