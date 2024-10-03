<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SellerProductsController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    #[Route('/seller/{id}', name: 'app_seller_products')]
    public function index(int $id): Response
    {
        $repositoryCategory = $this->entityManager->getRepository(User::class);
        $user = $repositoryCategory->find($id);
        if (!$user) {
            return $this->redirectToRoute('app_home_page');
        }

        $products = $user->getProducts();

        return $this->render('seller_products/index.html.twig', [
            'controller_name' => 'SellerProductsController',
            'products' => $products,
            'user' => $user,
        ]);
    }
}
