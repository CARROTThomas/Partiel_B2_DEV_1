<?php

namespace App\Entity;

use App\Repository\SuggestionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SuggestionRepository::class)]
class Suggestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["event:display"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["event:display"])]
    private ?string $product = null;

    #[ORM\ManyToOne(inversedBy: 'suggestions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\Column]
    #[Groups(["event:display"])]
    private ?bool $isTaken = null;

    #[ORM\OneToOne(inversedBy: 'suggestion', cascade: ['persist', 'remove'])]
    private ?Contribution $ofContribution = null;

    #[ORM\ManyToOne(inversedBy: 'suggestions')]
    #[Groups(["event:display"])]
    private ?Profile $isSupported = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function setProduct(string $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function isIsTaken(): ?bool
    {
        return $this->isTaken;
    }

    public function setIsTaken(bool $isTaken): static
    {
        $this->isTaken = $isTaken;

        return $this;
    }

    public function getOfContribution(): ?Contribution
    {
        return $this->ofContribution;
    }

    public function setOfContribution(?Contribution $ofContribution): static
    {
        $this->ofContribution = $ofContribution;

        return $this;
    }

    public function getIsSupported(): ?Profile
    {
        return $this->isSupported;
    }

    public function setIsSupported(?Profile $isSupported): static
    {
        $this->isSupported = $isSupported;

        return $this;
    }
}
