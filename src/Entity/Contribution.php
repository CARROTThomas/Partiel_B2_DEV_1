<?php

namespace App\Entity;

use App\Repository\ContributionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ContributionRepository::class)]
class Contribution
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["event:display"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["event:display"])]
    private ?string $product = null;

    #[ORM\ManyToOne(inversedBy: 'contributions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["event:display"])]
    private ?Profile $responsibleProfile = null;

    #[ORM\ManyToOne(inversedBy: 'contributions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\OneToOne(mappedBy: 'ofContribution', cascade: ['persist', 'remove'])]
    private ?Suggestion $suggestion = null;

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

    public function getResponsibleProfile(): ?Profile
    {
        return $this->responsibleProfile;
    }

    public function setResponsibleProfile(?Profile $responsibleProfile): static
    {
        $this->responsibleProfile = $responsibleProfile;

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

    public function getSuggestion(): ?Suggestion
    {
        return $this->suggestion;
    }

    public function setSuggestion(?Suggestion $suggestion): static
    {
        // unset the owning side of the relation if necessary
        if ($suggestion === null && $this->suggestion !== null) {
            $this->suggestion->setOfContribution(null);
        }

        // set the owning side of the relation if necessary
        if ($suggestion !== null && $suggestion->getOfContribution() !== $this) {
            $suggestion->setOfContribution($this);
        }

        $this->suggestion = $suggestion;

        return $this;
    }
}
