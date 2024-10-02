<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Category;
use App\Entity\Product;


class CategorySearchController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $entityManager) {}


    #[Route('/category/{categoryName}', name: 'app_category_search')]
    public function index(string $categoryName): Response
    {
        $repositoryCategory = $this->entityManager->getRepository(Category::class);
        $category = $repositoryCategory->findOneBy(['name' => $categoryName]);
        $products = $category->getProducts();







        return $this->render('category_search/index.html.twig', [
            'controller_name' => 'CategorySearchController',
            'products' => $products,
        ]);
    }
}
