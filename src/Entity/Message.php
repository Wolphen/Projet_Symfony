<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    /** message **/
    #[ORM\Column
    (
        type: Types::TEXT
    )]
    private ?string $message = null;


    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Chat $chat = null;

    #[ORM\ManyToOne(inversedBy: 'messages2')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;  // instead of string $Chat


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }



    public function getChat(): ?Chat
    {
        return $this->Chat;
    }

    public function setChat(Chat $Chat): static
    {
        $this->chat = $Chat;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }
}
