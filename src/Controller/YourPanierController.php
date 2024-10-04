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

        $repository = $this->entityManager->getRepository(Product::class);
        $productAddQuantity = $repository->findOneBy(['id' => $panier->getProduct()]);
        $productAddQuantity->setQuantity($productAddQuantity->getQuantity()+$panier->getQuantity());

        $this->entityManager->persist($panier);


        if ($panier->getQuantity()-$number <= 0) {
            $this->entityManager->remove($panier);
            $product = $panier->getProduct();
            $product->setActif(true);
            $this->entityManager->persist($panier);
        } else {
            $panier->setQuantity($panier->getQuantity()-$number);
        }
        
        $this->entityManager->flush();

        return $this->redirectToRoute('app_your_panier');
    }

    #[Route('/cart/buy/{id}', name: 'app_buy_cart')]
    public function buyCart(Request $request, User $id, Security $security)
    {
        $repository = $this->entityManager->getRepository(Panier::class);
        $panier = $repository->findAll(['user' => $id]);

        foreach ($panier as $key => $onePanier) {
            $product = $onePanier->getProduct();
            $productPrice = $product->getPrice();
            $panierQuantity = $onePanier->getQuantity();
            $productOwner = $product->getUser();
            $walletOwner = $productOwner->getWallet()+$productPrice*$panierQuantity;
            $productOwner->setWallet($walletOwner);

            $walletConnected = $id->getWallet()-$productPrice*$panierQuantity;
            $id->setWallet($walletConnected);

            $this->entityManager->persist($productOwner);
            $this->entityManager->persist($id);
            $this->entityManager->remove($onePanier);
            $this->entityManager->flush();
        }
        
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
            $repository = $this->entityManager->getRepository(Product::class);
            $productRemoveQuantity = $repository->findOneBy(['id' => $panier->getProduct()]);
            $quantity = $productRemoveQuantity->getQuantity()-$panier->getQuantity();
            $productRemoveQuantity->setQuantity($quantity);
            
            if ($quantity <= 0) {
                $productRemoveQuantity->setActif(false);
            }
            
            $this->entityManager->persist($panier);
            $this->entityManager->persist($productRemoveQuantity);
            $this->entityManager->flush();
            return $this->redirectToRoute("app_your_panier");
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
