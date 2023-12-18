<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["events:display", "event:display", "event:list:player", "requests:display", "profile:display"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["events:display", "event:display", "event:list:player", "requests:display", "profile:display"])]
    private ?string $displayName = null;

    #[ORM\OneToOne(inversedBy: 'profile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $profileUser = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Event::class)]
    private Collection $events;

    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'players')]
    private Collection $eventsAttending;

    #[ORM\OneToMany(mappedBy: 'recipient', targetEntity: Request::class, orphanRemoval: true)]
    private Collection $requests;

    #[ORM\OneToMany(mappedBy: 'responsibleProfile', targetEntity: Contribution::class, orphanRemoval: true)]
    private Collection $contributions;

    #[ORM\OneToMany(mappedBy: 'isSupported', targetEntity: Suggestion::class)]
    private Collection $suggestions;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->eventsAttending = new ArrayCollection();
        $this->requests = new ArrayCollection();
        $this->contributions = new ArrayCollection();
        $this->suggestions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): static
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getProfileUser(): ?User
    {
        return $this->profileUser;
    }

    public function setProfileUser(User $profileUser): static
    {
        $this->profileUser = $profileUser;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setOwner($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getOwner() === $this) {
                $event->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEventsAttending(): Collection
    {
        return $this->eventsAttending;
    }

    public function addEventsAttending(Event $eventsAttending): static
    {
        if (!$this->eventsAttending->contains($eventsAttending)) {
            $this->eventsAttending->add($eventsAttending);
            $eventsAttending->addPlayer($this);
        }

        return $this;
    }

    public function removeEventsAttending(Event $eventsAttending): static
    {
        if ($this->eventsAttending->removeElement($eventsAttending)) {
            $eventsAttending->removePlayer($this);
        }

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
            $request->setSender($this);
        }

        return $this;
    }

    public function removeRequest(Request $request): static
    {
        if ($this->requests->removeElement($request)) {
            // set the owning side to null (unless already changed)
            if ($request->getSender() === $this) {
                $request->setSender(null);
            }
        }

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
            $contribution->setResponsibleProfile($this);
        }

        return $this;
    }

    public function removeContribution(Contribution $contribution): static
    {
        if ($this->contributions->removeElement($contribution)) {
            // set the owning side to null (unless already changed)
            if ($contribution->getResponsibleProfile() === $this) {
                $contribution->setResponsibleProfile(null);
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
            $suggestion->setIsSupported($this);
        }

        return $this;
    }

    public function removeSuggestion(Suggestion $suggestion): static
    {
        if ($this->suggestions->removeElement($suggestion)) {
            // set the owning side to null (unless already changed)
            if ($suggestion->getIsSupported() === $this) {
                $suggestion->setIsSupported(null);
            }
        }

        return $this;
    }
}
