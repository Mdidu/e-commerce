<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", priority=10, name="register")
     */
    public function register(Request $request) {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        // $form->handleRequest($request);  
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
