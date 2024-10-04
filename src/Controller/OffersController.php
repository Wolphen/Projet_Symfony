<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Message;
use App\Entity\Product;
use App\Form\OffersType;
use App\Service\ChatService;
use App\Service\NotificationsService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class OffersController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {
    }

    #[Route('/offers/{id}', name: 'app_offers')]
    public function index(Product $id, Request $request, UserInterface $user, Security $security, ChatService $chatService, EntityManagerInterface $entityManager, NotificationsService $notificationsService): Response
    {
        $price = $id->getPrice();
        $minPrice = floor($price * 0.3);

        $defaultData = ['price' => $price];
        $form = $this->createFormBuilder($defaultData)
        ->add('price', IntegerType::class, [
            'attr' => [
                'min' => $minPrice,
                'max' => $price,
            ]
        ])
        ->add('Proposer', SubmitType::class)
        ->getForm();
        $form->handleRequest($request);


        $priceInitial = $id->getPrice();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $chat = $chatService->startChat($id, $entityManager, $user, $security);
            
            $message = new Message();
            $message->setMessage("Bonjour, je vous propose une offre de ".$data["price"]."€ pour ".$id->getName().". Qu'en pensez-vous ?");
            $message->setChat($chat);
            $message->setSender($user);
            $entityManager->persist($message);
            $entityManager->flush();

            $notificationsService->sendNotification(
                'OFFRE','Un utilisateur a propose une offre de '.$data["price"].'€ pour '.$id->getName().'.',$id->getUser(),$id
            );

            return $this->redirectToRoute('view_chat', ['id' => $chat->getId()]);
            

            return $this->redirectToRoute("app_your_products");
        }

        return $this->render('offers/index.html.twig', [
            'controller_name' => 'OffersController',
            'form' => $form,
            'product' => $id,
        ]);
    }
}
