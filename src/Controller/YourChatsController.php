<?php

namespace App\Controller;

use App\Entity\Chat;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class YourChatsController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/your/chats', name: 'app_your_chats')]
    public function index(): Response
    {
        $user = $this->getUser();
        $repositoryProduct = $this->entityManager->getRepository(Chat::class);
        $chats = $repositoryProduct->findBy(['user1' => $user]);
        if (!$chats){
            $chats = $repositoryProduct->findBy(['user2' => $user]);
        }

        return $this->render('your_chats/index.html.twig', [
            'controller_name' => 'YourChatsController',
            'chats' => $chats
        ]);
    }
}
