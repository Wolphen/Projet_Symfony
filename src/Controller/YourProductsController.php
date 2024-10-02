<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class YourProductsController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    #[Route('/your_products', name: 'app_your_products')]
    public function index(Security $security): Response
    {
        $userLogged = $security->getUser();
        
        $repositoryCategory = $this->entityManager->getRepository(User::class);
        $user = $repositoryCategory->findOneBy(['id' => $userLogged]);
        $products = $user->getProducts();

        return $this->render('seller_products/index.html.twig', [
            'controller_name' => 'SellerProductsController',
            'products' => $products,
            'user' => $user,
        ]);

        return $this->render('your_products/index.html.twig', [
            'controller_name' => 'YourProductsController',
        ]);
    }
}
