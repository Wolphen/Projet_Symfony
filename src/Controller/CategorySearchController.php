<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Category;

class CategorySearchController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    #[Route('/category/{categoryName}', name: 'app_category_search')]
    public function index(string $categoryName): Response
    {
        $repositoryCategory = $this->entityManager->getRepository(Category::class);
        $category = $repositoryCategory->findOneBy(['name' => $categoryName]);

        if (!$category) {
            $this->addFlash('error', 'La catégorie demandée n\'existe pas.');

            return $this->redirectToRoute('app_home_page');
        }

        $products = $category->getProducts();

        return $this->render('category_search/index.html.twig', [
            'controller_name' => 'CategorySearchController',
            'products' => $products,
            'categoryName' => $categoryName,
        ]);
    }
}
