<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $categorys = $repository->findAll();
        return $this->render('e_commerce/category.html.twig', [
            'categorys' => $categorys
        ]);
    }

    /**
     * @Route("/{name}", name="list_product")
     */
    public function listProduct($name)
    {
        // $repository = $this->getDoctrine()->getRepository(Category::class);

        // $category = $repository->find($name);

        return $this->render('e_commerce/listProductByCategory.html.twig', [
            'name' => $name
        ]);
    }
}
