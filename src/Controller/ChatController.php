<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Message;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\ChatRepository;
use App\Repository\MessageRepository;
use App\Service\ChatService;
use App\Service\NotificationsService;
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
    public function startChat(Product $id, EntityManagerInterface $entityManager, UserInterface $user, Security $security, ChatService $chatService): Response
    {
        $chat = $chatService->startChat($id, $entityManager,$user,$security);
        return $this->redirectToRoute('view_chat', ['id' => $chat->getId()]);
    }

    #[Route('/chat/{id}', name: 'view_chat', methods: ['GET', 'POST'])]
    public function viewChat(int $id, Request $request, EntityManagerInterface $entityManager, UserInterface $user, NotificationsService $notificationsService): Response
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
            if ($user->getUserIdentifier() == $chat->getUser2()->getEmail()) {
                $notificationsService->sendNotification(
                    'Message', $chat->getUser2()->getPseudo() .' vous à envoyé un message : '. $messageContent,
                    $chat->getUser1(), $chat->getProduct()
                );
            }else if ($user->getUserIdentifier() == $chat->getUser1()->getEmail()) {
                $notificationsService->sendNotification(
                    'Message', $chat->getUser1()->getPseudo() .' vous à envoyé un message : '. $messageContent,
                    $chat->getUser2(), $chat->getProduct()
                );
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
