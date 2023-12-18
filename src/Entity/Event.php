<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["events:display", "event:display", "requests:display", "events:attending:display"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["events:display", "event:display", "requests:display", "events:attending:display"])]
    private ?string $place = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["events:display", "event:display", "requests:display", "events:attending:display"])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["events:display", "event:display", "requests:display", "events:attending:display"])]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["events:display", "event:display", "requests:display", "events:attending:display"])]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\Column]
    private ?bool $statusPlace = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["events:display", "event:display", "requests:display", "events:attending:display"])]
    private ?Profile $owner = null;

    #[ORM\ManyToMany(targetEntity: Profile::class, inversedBy: 'eventsAttending')]
    #[Groups(["event:list:player", "event:display", "requests:display"])]
    private Collection $players;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Request::class, orphanRemoval: true)]
    private Collection $requests;

    #[ORM\Column]
    private ?bool $isPrivate = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Contribution::class, orphanRemoval: true)]
    #[Groups(["event:display"])]
    private Collection $contributions;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Suggestion::class, orphanRemoval: true)]
    #[Groups(["event:display"])]
    private Collection $suggestions;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->requests = new ArrayCollection();
        $this->contributions = new ArrayCollection();
        $this->suggestions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(string $place): static
    {
        $this->place = $place;

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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isStatusPlace(): ?bool
    {
        return $this->statusPlace;
    }

    public function setStatusPlace(bool $statusPlace): static
    {
        $this->statusPlace = $statusPlace;

        return $this;
    }

    public function getOwner(): ?Profile
    {
        return $this->owner;
    }

    public function setOwner(?Profile $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Profile>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Profile $player): static
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
        }

        return $this;
    }

    public function removePlayer(Profile $player): static
    {
        $this->players->removeElement($player);

        return $this;
    }

    /**
     * @return Collection<int, Request>
     */
    public function getRequests(): Collection
    {
        return $this->requests;
    }

    public function addRequest(Request $request): static
    {
        if (!$this->requests->contains($request)) {
            $this->requests->add($request);
            $request->setEvent($this);
        }

        return $this;
    }

    public function removeRequest(Request $request): static
    {
        if ($this->requests->removeElement($request)) {
            // set the owning side to null (unless already changed)
            if ($request->getEvent() === $this) {
                $request->setEvent(null);
            }
        }

        return $this;
    }

    public function isIsPrivate(): ?bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(bool $isPrivate): static
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }

    /**
     * @return Collection<int, Contribution>
     */
    public function getContributions(): Collection
    {
        return $this->contributions;
    }

    public function addContribution(Contribution $contribution): static
    {
        if (!$this->contributions->contains($contribution)) {
            $this->contributions->add($contribution);
            $contribution->setEvent($this);
        }

        return $this;
    }

    public function removeContribution(Contribution $contribution): static
    {
        if ($this->contributions->removeElement($contribution)) {
            // set the owning side to null (unless already changed)
            if ($contribution->getEvent() === $this) {
                $contribution->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Suggestion>
     */
    public function getSuggestions(): Collection
    {
        return $this->suggestions;
    }

    public function addSuggestion(Suggestion $suggestion): static
    {
        if (!$this->suggestions->contains($suggestion)) {
            $this->suggestions->add($suggestion);
            $suggestion->setEvent($this);
        }

        return $this;
    }

    public function removeSuggestion(Suggestion $suggestion): static
    {
        if ($this->suggestions->removeElement($suggestion)) {
            // set the owning side to null (unless already changed)
            if ($suggestion->getEvent() === $this) {
                $suggestion->setEvent(null);
            }
        }

        return $this;
    }
}
