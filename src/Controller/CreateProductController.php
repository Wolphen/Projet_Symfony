<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\CreateProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateProductController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    #[Route('/product/create', name: 'app_create_product')]
    public function index(Request $request): Response
    {
        $newProduct = new Product();

        $repositoryUser = $this->entityManager->getRepository(User::class);
        $getUser = $this->getUser();
        $form = $this->createForm(CreateProductType::class, $newProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newProduct->setUser($getUser);

            $this->entityManager->persist($newProduct);
            $this->entityManager->flush();
            return $this->redirectToRoute("app_your_products");
        }

        return $this->render('create_product/index.html.twig', [
            'controller_name' => 'CreateProductController',
            'form' => $form,
        ]);
    }
}
