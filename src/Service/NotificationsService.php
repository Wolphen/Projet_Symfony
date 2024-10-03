<?php

namespace App\Service;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;

class NotificationsService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function sendNotification(string $titre ,string $message, $recipient, $product): void
    {
        $notification = new Notification();
        $notification->setTitle($titre);
        $notification->setMessage($message);
        $notification->setUser($recipient);
        $notification->setProduct($product);
        $notification->setCreatedAt(new \DateTimeImmutable());
        $notification->setRead(false);
        $this->em->persist($notification);
        $this->em->flush();
    }
}