<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\SearchBarType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", priority=10, name="search")
     */
    public function search(EntityManagerInterface $entityManager, Request $request)
    {
        
        $search = $request->request->get('search');
        
        $repository = $entityManager->getRepository(Product::class);
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p.id, p.title, p.image, p.price, c.name')
                     ->from(Product::class, 'p')
                     ->leftJoin(category::class, 'c', 'WITH', 'c.id = p.category')
                     ->andWhere('p.title LIKE :search')
                     ->setParameter('search', $search.'%');

        return $this->render('search/index.html.twig', [
            'results' => $queryBuilder->getQuery()->getScalarResult()
        ]);
    }
}
