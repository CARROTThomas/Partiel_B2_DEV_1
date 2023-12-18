<?php

namespace App\Controller;

use App\Entity\Contribution;
use App\Entity\Event;
use App\Entity\Suggestion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/suggestion')]
class SuggestionController extends AbstractController
{
    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param SerializerInterface $serializer
     * @param Event $event
     * @return Response
     * Create a suggestion
     */
    #[Route('/add/{event_id}', name: 'app_suggestion_add')]
    public function create(
        Request $request,
        EntityManagerInterface $manager,
        SerializerInterface $serializer,
        #[MapEntity(id: 'event_id')] Event $event,
    ): Response
    {
        if (!$event->isIsPrivate()) {return $this->json("vous ne pouvez pas ajouter de contribution à un event public",200);}

        if ($this->getUser()->getProfile() === $event->getOwner()) {

            $newSuggestion = $serializer->deserialize($request->getContent(),Suggestion::class,"json");
            $newSuggestion->setIsTaken(false);
            $newSuggestion->setEvent($event);

            $manager->persist($newSuggestion);
            $manager->flush();

            return $this->json("suggestion crée", 201);
        }

        return $this->json("vous ne pouvez pas ajouter de suggestion dans ce groupe car vous n'etes pas le proprio", 200);
    }

    /**
     * @param EntityManagerInterface $manager
     * @param Suggestion $suggestion
     * @return Response
     * Create a contribution with the suggestion data with additional information from the user
     */
    #[Route('/accept/{suggestion_id}', name: 'app_suggestion_accept')]
    public function accept(
        EntityManagerInterface $manager,
        #[MapEntity(id: 'suggestion_id')] Suggestion $suggestion,
    ): Response
    {
        if (!$suggestion->getEvent()->isIsPrivate()) {return $this->json("vous ne pouvez pas ajouter de contribution à un event public",200);}

        foreach ($suggestion->getEvent()->getPlayers() as $player) {
            if ($this->getUser()->getProfile() === $player) {

                $newContribution = new Contribution();

                $newContribution->setSuggestion($suggestion);
                $newContribution->setEvent($suggestion->getEvent());
                $newContribution->setProduct($suggestion->getProduct());
                $newContribution->setResponsibleProfile($this->getUser()->getProfile());

                $suggestion->setIsTaken(true);
                $suggestion->setOfContribution($newContribution);
                $suggestion->setIsSupported($this->getUser()->getProfile());

                $manager->persist($newContribution);
                $manager->persist($suggestion);
                $manager->flush();

                return $this->json("suggestion et contribution mis à jour, merci", 200);
            }
        }
        return $this->json("vous ne pouvez pas acceptez cette suggestion, vous n'etes pas dans le groupe", 200);
    }
}
