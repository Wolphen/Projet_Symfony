<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security, private readonly EntityManagerInterface $entityManager)
    {
        $this->security = $security;
    }

    #[Route('/chat/{product_id}', name: 'app_chat')]
    public function index(int $product_id): Response
    {
        // Récupérer le produit via l'ID
        $product = $this->entityManager->getRepository(Product::class)->find($product_id);

        // Si le produit n'existe pas, retourner une erreur
        if (!$product) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        // Récupérer le propriétaire du produit (receiver)
        $receiver = $product->getUser();

        // Récupérer l'utilisateur connecté (sender ou receiver)
        /** @var User $user */
        $user = $this->getUser();

        $messages = $this->entityManager->getRepository(Message::class)->findBy([
            'product' => $product,
            'receiver' => $user,
        ]);

        if ($user !== $receiver && !$messages) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à voir cette conversation.');
            return $this->redirectToRoute('app_home_page');
        }

        return $this->render('chat/index.html.twig', [
            'product' => $product,
            'owner' => $receiver,
            'messages' => $messages,
        ]);
    }


    #[Route('/chat/send/{product_id}', name: 'chat_send', methods: ['POST'])]
    public function send(Request $request, int $product_id): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($product_id);

        if (!$product) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        $receiver = $product->getUser();

        /** @var User $sender */
        $sender = $this->getUser();


        $messageContent = $request->request->get('message');
        if (empty($messageContent)) {
            $this->addFlash('error', 'Le message ne peut pas être vide.');
            return $this->redirectToRoute('app_chat', ['product_id' => $product_id]);
        }

        $message = new Message();
        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setProduct($product);
        $message->setMessage($messageContent);

        // Sauvegarder le message dans la base de données
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        // Rediriger vers la page de chat pour afficher les messages
        return $this->redirectToRoute('app_chat', ['product_id' => $product_id]);
    }
}
