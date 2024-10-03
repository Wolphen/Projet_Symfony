<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OneProductController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    #[Route('/product/{id}', name: 'app_one_product')]
    public function index(int $id): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            $this->addFlash('error', 'Produit non trouvé.');
            return $this->redirectToRoute('app_home_page');
        }

        $category = $product->getCategory();
        if (!$category) {
            $this->addFlash('error', 'Aucune catégorie trouvée.');
            return $this->redirectToRoute('app_home_page');
        }

        return $this->render('one_product/index.html.twig', [
            'controller_name' => 'OneProductController',
            'product' => $product,
            'category' => $category,
        ]);
    }
}
