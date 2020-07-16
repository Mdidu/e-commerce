<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ECommerceController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('e_commerce/index.html.twig', [
            'controller_name' => 'ECommerceController',
        ]);
    }

    /**
     * @Route("/category", name="category")
     */
    public function category()
    {
        return $this->render('e_commerce/category.html.twig');
    }
}
