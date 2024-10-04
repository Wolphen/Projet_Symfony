<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\DeleteProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeleteProductController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    #[Route('/product/delete/{id}', name: 'app_delete_product')]
    public function index(Product $id, Request $request): Response
    {

        $form = $this->createForm(DeleteProductType::class, $id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($id);
            $this->entityManager->flush();
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute("app_home_page");
            } else {
                return $this->redirectToRoute("app_your_products");
            }
        }

        return  $this->render('delete_product/index.html.twig', [
            'controller_name' => 'DeleteProductController',
            'form' => $form->createView(),
            'product' => $id,
            'user' => $id->getUser()->getId(),
        ]);
    }
}
