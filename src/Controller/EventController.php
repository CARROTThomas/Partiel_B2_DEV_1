<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/event')]
class EventController extends AbstractController
{
    /**
     * @param EventRepository $eventRepository
     * @return Response
     * Displays the list of public events
     */
    #[Route('/public/all', name: 'app_event_all')]
    public function all(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy(["isPrivate"=>false]);

        return $this->json($events, 200, [], ["groups"=>"events:display"]);
    }

    #[Route('/show/{event_id}', name: 'app_event_show')]
    public function show(
        #[MapEntity(id: 'event_id')] Event $event,
    ): Response
    {
        return $this->json($event, 200, [], ["groups"=>"event:display"]);
    }

    /**
     * @param EntityManagerInterface $manager
     * @param Event $event
     * @return Response
     * Join a public event
     */
    #[Route('/join/{event_id}', name: 'app_event_join')]
    public function joinEvent(
        EntityManagerInterface $manager,
        #[MapEntity(id: 'event_id')] Event $event,
    ): Response
    {
        $date = new \DateTime();

        if (!$event->isStatus()) {return $this->json("ce concert est annulé et donc ne peut être rejoint", 200);}
        if ($event->getStartDate() <= $date) {return $this->json("l'event à déjà commencer, vous ne pouvez plus participer !", 200);}
        if (!$this->getUser()) {return $this->json("connectez-vous d'abord !", 200);}
        if ($event->isIsPrivate()) {return $this->json("évènement privée, il vous faut une invite", 200);}

        foreach ($event->getPlayers() as $player) {
            if ($player === $this->getUser()->getProfile()) {
                return $this->json("vous etes déjà dans cette évent", 200);
            }
        }

        $event->addPlayer($this->getUser()->getProfile());
        $manager->persist($event);
        $manager->flush();

        return $this->json("vous etes dorénavant inscrit !", 200);
    }

    /**
     * @param Event $event
     * @return Response
     * Retrieve the list of participants by event
     */
    #[Route('/list/{event_id}', name: 'app_event_list_player')]
    public function playerList(
        #[MapEntity(id: 'event_id')] Event $event,
    ): Response
    {
        $listPlayers = $event->getPlayers();
        return $this->json($listPlayers, 200, [], ["groups"=>"event:list:player"]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param SerializerInterface $serializer
     * @return Response
     * Create an event
     */
    #[Route('/create', name: 'app_event_create_event', methods: "POST")]
    public function createEvent(
        Request $request,
        EntityManagerInterface $manager,
        SerializerInterface $serializer,
    ):Response{

        $newEvent = $serializer->deserialize($request->getContent(),Event::class,"json");

        $newEvent->setOwner($this->getUser()->getProfile());
        $newEvent->addPlayer($this->getUser()->getProfile());
        $newEvent->setStatus(true);


        if ($newEvent->getEndDate() < $newEvent->getStartDate() ){
            return $this->json("date incorecte => la date de fin doit-être après de celle de fin", 403);

        }elseif ($newEvent->getStartDate() < new \DateTime()){
            return $this->json("un évènement ne peut creer avec une date passé", 403);

        }else{
            $manager->persist($newEvent);
            $manager->flush();
        }

        return $this->json("event créer", 201);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param SerializerInterface $serializer
     * @param Event $event
     * @return Response
     * Modified the dates and status of an event
     */
    #[Route('/edit/{event_id}', name: 'app_event_edit_event', methods: "POST")]
    public function edit(
        Request $request,
        EntityManagerInterface $manager,
        SerializerInterface $serializer,
        #[MapEntity(id: 'event_id')] Event $event,
    ):Response{

        if ($this->getUser()->getProfile() === $event->getOwner()) {

            $eventEdit = $serializer->deserialize($request->getContent(),Event::class,"json", array("object_to_populate"=>$event));

            if ($eventEdit->getEndDate() < $eventEdit->getStartDate() ){
                return $this->json("date incorecte => la date de fin doit-être après de celle de fin", 403);

            }elseif ($eventEdit->getStartDate() < new \DateTime()){
                return $this->json("un évènement ne peut creer avec une date passé", 403);

            }else{
                $manager->persist($eventEdit);
                $manager->flush();
            }
            return $this->json("évènement modifié", 200);
        }

        return $this->json("vous n'etes pas le proprio de cet évènement", 200);
    }
}
