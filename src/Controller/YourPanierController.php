<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Product;
use App\Entity\User;
use App\Form\AddToPanierType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class YourPanierController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    #[Route('/cart/remove/{id}', name: 'app_remove_cart')]
    public function removeCart(Request $request, Panier $id, Security $security)
    {
        $number = $request->query->get('number', 1);

        $repository = $this->entityManager->getRepository(Panier::class);
        $panier = $repository->findOneBy(['id' => $id]);

        if ($panier->getQuantity()-$number <= 0) {
            $this->entityManager->remove($panier);
        } else {
            $panier->setQuantity($panier->getQuantity()-$number);
        }
        
        $this->entityManager->flush();

        return $this->redirectToRoute('app_your_panier');
    }

    #[Route('/cart/add/{id}', name: 'app_add_cart')]
    public function addCart(Request $request, Product $id,Security $security)
    {
        $route = $request->query->get('route', 'app_home_page');

        $userLogged = $security->getUser();
        $repositoryCategory = $this->entityManager->getRepository(User::class);
        $user = $repositoryCategory->findOneBy(['id' => $userLogged]);

        $panier = new Panier();
        $panier->setProduct($id);
        $panier->setQuantity(1);
        $panier->setUser($user);

        $form = $this->createForm(AddToPanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($panier);
            $this->entityManager->flush();
            return $this->redirectToRoute("app_your_products");
        }

        return $this->render('cart_add/index.html.twig', [
            'controller_name' => 'ModifyProductController',
            'panier' => $panier,
            'product' => $id,
            'form' => $form,
        ]);
    }

    #[Route('/your_cart', name: 'app_your_panier')]
    public function index(Security $security): Response
    {
        $userLogged = $security->getUser();
        $repositoryUser = $this->entityManager->getRepository(User::class);
        $user = $repositoryUser->findOneBy(['id' => $userLogged]);

        $repositoryFavorites = $this->entityManager->getRepository(Panier::class);
        $paniers = $repositoryFavorites->findBy(['user' => $user]);
        //dd($paniers);

        return $this->render('your_panier/index.html.twig', [
            'controller_name' => 'YourPanierController',
            'paniers' => $paniers,
            'user' => $user,
        ]);
    }
}
