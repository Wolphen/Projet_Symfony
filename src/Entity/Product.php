<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** name **/
    #[
        ORM\Column(
            length: 80
        )
    ]
    private ?string $name = null;

    /** description **/
    #[ORM\Column(
        type: Types::TEXT
    )]
    private ?string $description = null;

    /** image **/
    #[ORM\Column(
        length: 255
    )]
    private ?string $image = null;

    /** price **/
    #[ORM\Column]
    private ?float $price = null;

    /** quantity **/
    #[ORM\Column]
    private ?int $quantity = null;

    /** actif **/
    #[ORM\Column]
    private ?bool $actif = null;

    /** tva **/
    #[ORM\Column]
    private ?float $tva = null;

    /** user **/
    #[ORM\ManyToOne(
        inversedBy: 'products'
    )]

    #[ORM\JoinColumn(
        nullable: false
    )]
    private ?User $user = null;

    /** category **/
    #[ORM\ManyToOne(
        inversedBy: 'products'
    )]
    #[ORM\JoinColumn(
        nullable: false
    )]
    private ?Category $category = null;



    /**
     * @var Collection<int, Favoris>
     */
    #[ORM\OneToMany(targetEntity: Favoris::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $favoris;

    /**

     * @var Collection<int, Chat>
     */
    #[ORM\OneToMany(targetEntity: Chat::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $chats;

    /**
     * @var Collection<int, Panier>
     */
    #[ORM\OneToMany(targetEntity: Panier::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $paniers;


    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->favoris = new ArrayCollection();
        $this->chats = new ArrayCollection();
        $this->paniers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(float $tva): static
    {
        $this->tva = $tva;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setProduct($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getProduct() === $this) {
                $message->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Favoris>
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Favoris $favori): static
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris->add($favori);
            $favori->setProduct($this);
        }

        return $this;
    }

    public function removeFavori(Favoris $favori): static
    {
        if ($this->favoris->removeElement($favori)) {
            // set the owning side to null (unless already changed)
            if ($favori->getProduct() === $this) {
                $favori->setProduct(null);
            }
        }

        return $this;
    }

    /**

     * @return Collection<int, Chat>
     */
    public function getChats(): Collection
    {
        return $this->chats;
    }

    public function addChat(Chat $chat): static
    {
        if (!$this->chats->contains($chat)) {
            $this->chats->add($chat);
            $chat->setProduct($this);
        }
        return $this;
    }



    /**
     * @return Collection<int, Panier>
     */
    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    public function addPanier(Panier $panier): static
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers->add($panier);
            $panier->setProduct($this);
        }

        return $this;
    }


    public function removeChat(Chat $chat): static
    {
        if ($this->chats->removeElement($chat)) {
            // set the owning side to null (unless already changed)
            if ($chat->getProduct() === $this) {
                $chat->setProduct(null);
            }
        }
        return $this;
    }

    public function removePanier(Panier $panier): static
    {
        if ($this->paniers->removeElement($panier)) {
            // set the owning side to null (unless already changed)
            if ($panier->getProduct() === $this) {
                $panier->setProduct(null);
            }
        }

        return $this;
    }
}
