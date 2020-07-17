<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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

    /**
     * @Route("/{name}/new", name="create_product")
     */
    public function createProduct($name,
                                Request $request,
                                EntityManagerInterface $manager)
    {
        $product = new Product();
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $categorys = $repository->findAll();

        $form = $this->createFormBuilder($product)
            ->add('title')
            ->add('description')
            ->add('image', TextType::class, ['required' => false])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('price', NumberType::class)
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $product->setCreatedAt(new \DateTime());

            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute('list_product', 
            ['id' => $product->getCategory()]);
        }

        return $this->render('e_commerce/createProduct.html.twig', [
            'formProduct' => $form->createView(),
            'categorys' => $categorys
        ]);
    }
    /**
     * afficher les produits dans la liste
     * créer page produit
     * design global du site
     * search bar
     * home page
     * les logs
     * panier
     * commande
     */
}
