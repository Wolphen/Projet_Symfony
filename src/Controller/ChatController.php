<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Message;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\ChatRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ChatController extends AbstractController
{
    #[Route('/product/{id}/start-chat', name: 'start_chat', methods: ['POST'])]
    public function startChat(Product $id, EntityManagerInterface $entityManager, UserInterface $user, Security $security): Response
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
            return $this->redirectToRoute('view_chat', ['id' => $existingChat->getId()]);
        }

        // Create a new chat
        $chat = new Chat();
        $chat->setUser1($sender);
        $chat->setUser2($owner);
        $chat->setProduct($product);
        $entityManager->persist($chat);
        $entityManager->flush();

        return $this->redirectToRoute('view_chat', ['id' => $chat->getId()]);
    }

    #[Route('/chat/{id}', name: 'view_chat', methods: ['GET', 'POST'])]
    public function viewChat(int $id, Request $request, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        $chat = $entityManager->getRepository(Chat::class)->findOneBy(['id'=>$id]);

        $entityManager->initializeObject($chat->getUser2());
        $entityManager->initializeObject($chat->getProduct());

        // Check if the current user is part of the chat
        if ($chat->getUser1() !== $user && $chat->getUser2() !== $user) {
            throw $this->createAccessDeniedException();
        }


        // Handle form submission for a new message
        if ($request->isMethod('POST')) {
            $messageContent = $request->request->get('message');
            if (!empty($messageContent)) {
                $message = new Message();
                $message->setMessage($messageContent);
                $message->setChat($chat);
                $message->setSender($user);
                $entityManager->persist($message);
                $entityManager->flush();
            }

            return $this->redirectToRoute('view_chat', ['id' => $chat->getId()]);
        }

        // Retrieve messages for this chat
        $messages = $entityManager->getRepository(Message::class)->findBy(['chat' => $chat]);

        return $this->render('chat/view.html.twig', [
            'chat' => $chat,
            'messages' => $messages,
            'product' => $chat->getProduct(),
            'connectedUser' => $user,
        ]);
    }

    #[Route('/my-chats', name: 'my_chats')]
    public function myChats(ChatRepository $chatRepository, UserInterface $user): Response
    {
        // Get all chats where the current user is the sender or receiver
        $chats = $chatRepository->findBySenderOrReceiver($user);

        return $this->render('chat/my_chats.html.twig', [
            'chats' => $chats,
        ]);
    }
}
