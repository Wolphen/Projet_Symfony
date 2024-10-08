<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;

class CreateDataController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/create/data', name: 'app_create_data')]
    public function index(): Response
    {
        $repositoryUser = $this->entityManager->getRepository(User::class);
        $repositoryProduct = $this->entityManager->getRepository(Product::class);
        $repositoryCategory = $this->entityManager->getRepository(Category::class);
        $getUser = $repositoryUser->findOneById(1);
        $getProduct = $repositoryProduct->findOneById(1);



        $category = new Category();
        $category->setName('Humain');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $getCategory = $repositoryCategory->findOneById(8);

        $product = new Product();
        $product->setName('Truck');
        $product->setDescription('Le plus beau short Cars  au monde');
        $product->setImage('https://ae-pic-a1.aliexpress-media.com/kf/Sbbc7c3e6da5242fe9dfa5d0297e2b045q.jpg_80x80.jpg_.webp');
        $product->setPrice(29);
        $product->setQuantity(1);
        $product->setActif(true);
        $product->setTva(20);
        $product->setUser($getUser);
        $product->setCategory($getCategory);
        $this->entityManager->persist($product);
        $this->entityManager->flush();


        return $this->render('create_data/index.html.twig', [
            'controller_name' => 'CreateDataController',
            'user' => $getUser,
            'product' => $getProduct,
        ]);
    }
}
