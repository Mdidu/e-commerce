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
// use Symfony\Component\Form\Extension\Core\Type\SearchType;

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
     * @Route("/new", name="create_product")
     */
    public function createProduct(Request $request,
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

            $category = $repository->find($product->getCategory());

            return $this->redirectToRoute('list_product', ['name' => $category->getName(), 
            'category' => $category]);
        }

        return $this->render('e_commerce/createProduct.html.twig', [
            'formProduct' => $form->createView(),
            'categorys' => $categorys
        ]);
    }
    
    /**
     * @Route("/search", name="search")
     */
    public function search(EntityManagerInterface $entityManager, Request $request)
    {
        // $product = new Product();
        // $form = $this->createForm(SearchType::class, $product);
        
        $search = $request->request->get('search');
        
        $repository = $entityManager->getRepository(Product::class);
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p.id, p.title, p.image, p.price, c.name')
                     ->from(Product::class, 'p')
                     ->leftJoin(category::class, 'c', 'WITH', 'c.id = p.category')
                     ->andWhere('p.title LIKE :search')
                     ->setParameter('search', $search.'%');

        $res = $queryBuilder->getQuery()->getScalarResult();
        // dump($res);

        return $this->render('search/index.html.twig', [
            'results' => $res
            // 'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{name}", name="list_product")
     */
    public function listProduct($name, Category $category)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->findAll();
        
        return $this->render('e_commerce/listProductByCategory.html.twig', [
            'category' => $category,
            'products' => $product
        ]);
    }
    /**
     * @Route("/{name}/{id}", name="product")
     */
    public function product($name, $id, Product $product)
    {
        return $this->render('e_commerce/product.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * search bar
     * les logs
     * panier
     * home page
     * design global du site
     * commande
     */
}
