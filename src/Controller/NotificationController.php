<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NotificationController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    #[Route('/notification', name: 'app_notification')]
    public function index( Security $security): Response
    {
        $user = $security->getUser();
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        if ($security->getUser() !== $user) {
            return $this->redirectToRoute('app_login');
        }

        $notifs = $this->entityManager->getRepository(Notification::class)->findBy([
            'user' => $user
        ]);

        return $this->render('notification/index.html.twig', [
            'controller_name' => 'NotificationController',
            'notifications' => $notifs,
            'user' => $user,
        ]);
    }
    #[Route('/notification{id}', name: 'app_notification_mark_as_read')]
    public function markAsRead(Notification $id): Response
    {
        $id->setRead(true);
        $this->entityManager->persist($id);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_notification');
    }
}
