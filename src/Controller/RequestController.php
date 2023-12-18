<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Profile;
use App\Entity\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/request')]
class RequestController extends AbstractController
{
    /**
     * @param EntityManagerInterface $manager
     * @param Profile $profile
     * @param Event $event
     * @return Response
     * Sends a private event invitation to a user chosen in the app
     */
    #[Route('/send/{profile_id}/event/{event_id}', name: 'send_request')]
    public function sendRequest(
        EntityManagerInterface $manager,
        #[MapEntity(id: 'profile_id')] Profile $profile,
        #[MapEntity(id: 'event_id')] Event $event,
    ): Response
    {
        if (!$event->isStatus()) {return $this->json("ce concert est annulé et donc ne peut être rejoint", 200);}
        if (!$this->getUser()->getProfile() === $event->getOwner()) {return $this->json("vous n'avez pas les droit pour inviter", 200);}
        if ($this->getUser()->getProfile() === $profile) {return $this->json("sender === recipient; vous ne pouvez pas vous invitez vous même", 200);}

        $request = new Request();

        $request->setSender($this->getUser()->getProfile());
        $request->setRecipient($profile);
        $request->setEvent($event);

        $manager->persist($request);
        $manager->flush();

        return $this->json("request send",200);
    }

    /**
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     * Accepted an invitation to an event
     */
    #[Route('/accept/{request_id}', name: 'accept_request')]
    public function acceptRequest(
        EntityManagerInterface $manager,
        #[MapEntity(id: 'request_id')] Request $request,
    ): Response
    {
        $date = new \DateTime();
        $event  = $request->getEvent();

        if (!$event->isStatus()) {return $this->json("ce concert est annulé et donc ne peut être rejoint", 200);}
        if ($this->getUser()->getProfile() != $request->getRecipient()) {return $this->json("vous n'étes pas concerné par cette demande", 200);}
        if ($request->getEvent()->getStartDate() <= $date) {return $this->json("l'event à déjà commencer, vous ne pouvez plus participer !", 200);}

        $event->addPlayer($this->getUser()->getProfile());

        $manager->persist($event);
        $manager->remove($request);
        $manager->flush();

        return $this->json("Bienvenue dans l'event !",200);
    }

    /**
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     * Refused an invitation to an event
     */
    #[Route('/refuse/{request_id}', name: 'refuse_request')]
    public function refuseRequest(
        EntityManagerInterface $manager,
        #[MapEntity(id: 'request_id')] Request $request,
    ):Response
    {
        $manager->remove($request);
        $manager->flush();

        return $this->json("invitation refusé.");
    }

    #[Route('/promote/{profile_id}/event/{event_id}', name: 'refuse_request')]
    public function promote(
        EntityManagerInterface $manager,
        #[MapEntity(id: 'profile_id')] Profile $profile,
        #[MapEntity(id: 'event_id')] Event $event,
    ):Response
    {
        if ($event->getOwner() != $this->getUser()->getProfile()) {return $this->json("vous n'avez pas les droits de proumouvoir dans cette évènement", 200);}



        $manager->remove($profile);
        $manager->flush();

        return $this->json("user promote to -admin");
    }
}
