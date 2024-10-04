<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;

class TransactionService
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function InsertTransaction(string $type, $recipient, $montant): void
    {
        $notification = new Transaction();
        $notification->setDate(new \DateTime());
        $notification->setType($type);
        $notification->setUser($recipient);
        $notification->setMontant($montant);
        $this->em->persist($notification);
        $this->em->flush();
    }

}