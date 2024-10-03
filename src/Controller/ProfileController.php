<?php

namespace App\Controller;

use App\Entity\Favoris;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/profile/{userName}', name: 'app_profile')]
    public function index(string $userName): Response
    {
        $nom_user = $this->entityManager->getRepository(User::class)->findOneBy(['pseudo' => $userName]);
        $user = $this->getUser();

        if ($user != null){
            $infoFavori = $this->entityManager->getRepository(Favoris::class)->count(["user" => $user->getId()]);
            return $this->render('profile/index.html.twig', [
                'controller_name' => 'ProfileController',
                'user' => $user,
                'infoFavori' => $infoFavori,
                'nom_user' => $nom_user,
            ]);
        }
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);

    }
}
