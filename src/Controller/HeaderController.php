<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\SearchBarType;

class HeaderController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function headerData(): Response
    {
        $repositoryProduct = $this->entityManager->getRepository(Product::class);
        $products = $repositoryProduct->findAll();
        $testForm = $this->createForm(SearchBarType::class);
        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        return $this->render('header/index.html.twig', [
            'products' => $products,
            'testForm' => $testForm,
            'categories' => $categories,

        ]);
    }

    public function category(string $categoryName): Response
    {
        $repositoryCategory = $this->entityManager->getRepository(Category::class);
        $category = $repositoryCategory->findOneBy(['name' => $categoryName]);

        if ($category) {
            $products = $category->getProducts();
        } else {
            $products = [];
        }

        return $this->render('category_search/index.html.twig', [
            'controller_name' => 'CategorySearchController',
            'products' => $products,
        ]);
    }
}
