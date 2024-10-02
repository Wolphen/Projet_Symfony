<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ModifyProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ModifyProductController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    #[Route('/product/modify/{id}', name: 'app_modify_product')]
    public function index(Product $modifiedProduct, Request $request): Response
    {

        $form = $this->createForm(ModifyProductType::class, $modifiedProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($modifiedProduct);
            $this->entityManager->flush();
            return $this->redirectToRoute("app_your_products");
        }

        return $this->render('modify_product/index.html.twig', [
            'controller_name' => 'ModifyProductController',
            'product' => $modifiedProduct,
            'form' => $form,
            'user' => $modifiedProduct->getUser()->getId(),
        ]);
    }
}
