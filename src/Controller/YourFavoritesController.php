<?php

namespace App\Controller;

use App\Entity\Favoris;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class YourFavoritesController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    #[Route('/your_favorites', name: 'app_your_favorites')]
    public function index(Security $security): Response
    {
        $userLogged = $security->getUser();
        
        $repositoryUser = $this->entityManager->getRepository(User::class);
        $user = $repositoryUser->findOneBy(['id' => $userLogged]);

        $repositoryFavorites = $this->entityManager->getRepository(Favoris::class);
        $favorites = $repositoryFavorites->findBy(['user' => $user]);

        //dd($favorites);

        return $this->render('your_favorites/index.html.twig', [
            'controller_name' => 'YourFavoritesController',
            'favorites' => $favorites,
            'user' => $user,
        ]);
    }
}
