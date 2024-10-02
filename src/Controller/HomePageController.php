<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;




class HomePageController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/', name: 'app_home_page')]
    public function index(): Response
    {
        $repositoryUser = $this->entityManager->getRepository(User::class);
        $repositoryCategory = $this->entityManager->getRepository(Category::class);
        $repositoryProduct = $this->entityManager->getRepository(Product::class);
        $getUser = $repositoryUser->findOneById(1);
        $getCategory = $repositoryCategory->findOneById(1);
        $getProduct = $repositoryProduct->findOneById(1);





        // $product = new Product();
        // $product->setName('Crocs');
        // $product->setDescription('Les plus belles crocs de ta vie');
        // $product->setImage('https://ae-pic-a1.aliexpress-media.com/kf/Sbbc7c3e6da5242fe9dfa5d0297e2b045q.jpg_80x80.jpg_.webp');
        // $product->setPrice(29);
        // $product->setQuantity(1);
        // $product->setActif(true);
        // $product->setTva(20);
        // $product->setUser($getUser);
        // $product->setCategory($getCategory);
        // $this->entityManager->persist($product);
        // $this->entityManager->flush();


        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'user' => $getUser,
            'product' => $getProduct,
        ]);
    }
}
