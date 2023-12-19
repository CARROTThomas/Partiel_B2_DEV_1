<?php

namespace App\Controller;

use App\Entity\Contribution;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/contribution')]
class ContributionController extends AbstractController
{
    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param SerializerInterface $serializer
     * @param Event $event
     * @return Response
     * Create a contribution in event
     */
    #[Route('/create/{event_id}', name: 'app_contribution_create')]
    public function create(
        Request $request,
        EntityManagerInterface $manager,
        SerializerInterface $serializer,
        #[MapEntity(id: 'event_id')] Event $event,
    ): Response
    {
        if (!$event->isIsPrivate()) {return $this->json("vous ne pouvez pas ajouter de contribution à un event public",200);}

        foreach ($event->getPlayers() as $player) {
            if ($this->getUser()->getProfile() === $player){

                $newContribution = $serializer->deserialize($request->getContent(),Contribution::class,"json");
                $newContribution->setResponsibleProfile($this->getUser()->getProfile());
                $newContribution->setEvent($event);

                $manager->persist($newContribution);
                $manager->flush();

                return $this->json("contribution créer au groupe !", 201);
            }
        }

        return $this->json("vous ne faites pas partie du groupe", 200);
    }
}
