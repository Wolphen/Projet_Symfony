<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OneProductController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        
    }

    #[Route('/product/{id}', name: 'app_one_product')]
    public function index(Product $id): Response
    {
        $repository = $this->entityManager->getRepository(Category::class);
        $category = $repository->findBy(['id' => $id->getCategory()->getId()]);

        return $this->render('one_product/index.html.twig', [
            'controller_name' => 'OneProductController',
            'product' => $id,
            'category' => $category,
        ]);
    }
}
