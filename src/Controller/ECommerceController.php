<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Entity\ShoppingCart;
use App\Service\Cart\CartService;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
        return $this->render('e_commerce/category.html.twig', [
            'categorys' => $this->getDoctrine()->getRepository(Category::class)->findAll()
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

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $product->setCreatedAt(new \DateTime());

            $manager->persist($product);
            $manager->flush();

            $category = $repository->find($product->getCategory());

            return $this->redirectToRoute('list_product', [
                'name' => $category->getName(), 
                'category' => $category
            ]);
        }

        return $this->render('e_commerce/createProduct.html.twig', [
            'formProduct' => $form->createView(),
            'categorys' => $repository->findAll()
        ]);
    }
    
    /**
     * @Route("/panier", name="shopping_cart")
     */
    public function shoppingCart(CartService $cartService) {

        return $this->render('e_commerce/shoppingCart.html.twig', [
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal()
        ]);
    }

    /**
     * @Route("/panier/remove/{id}", name="cart_remove")
     */
    public function remove($id, CartService $cartService) {
        
        $cartService->remove($id);

        return $this->redirectToRoute('shopping_cart');
    }

    /**
     * @Route("/{name}", name="list_product")
     */
    public function listProduct($name, Category $category)
    {
        
        return $this->render('e_commerce/listProductByCategory.html.twig', [
            'category' => $category,
            'products' => $this->getDoctrine()->getRepository(Product::class)->findAll()
        ]);
    }
    /**
     * @Route("/{name}/{id}", name="product")
     */
    public function product($name, 
                            $id, 
                            Product $product, 
                            Request $request, 
                            CartService $cartService)
    {
        if($request->get('userId') || $request->get('productId')) {
        
            $cartService->add($id);

            return $this->redirectToRoute('shopping_cart');
        }
        
        return $this->render('e_commerce/product.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * home page
     * commande
     * auto completion search bar
     */
}
