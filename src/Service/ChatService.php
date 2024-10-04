<?php

namespace App\Service;

use App\Entity\Chat;
use App\Entity\Notification;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ChatService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function startChat(Product $id, EntityManagerInterface $entityManager, UserInterface $user, Security $security): Chat
    {
        // Get the product owner
        $product = $entityManager->getRepository(Product::class)->find($id);
        $owner = $entityManager->getRepository(User::class)->findOneBy(['id' => $product->getUser()->getId()]);


        $sender = $entityManager->getRepository(User::class)->find($security->getUser());


        // Check if a chat already exists between these two users
        $existingChat = $entityManager->getRepository(Chat::class)->findOneBy([
            'user1' => $sender,
            'user2' => $owner,
        ]) ?? $entityManager->getRepository(Chat::class)->findOneBy([
            'user1' => $owner,
            'user2' => $sender,
        ]);

        if ($existingChat) {
            return $existingChat;
        }

        // Create a new chat
        $chat = new Chat();
        $chat->setUser1($sender);
        $chat->setUser2($owner);
        $chat->setProduct($product);
        $entityManager->persist($chat);
        $entityManager->flush();
        return $chat;
    }
}