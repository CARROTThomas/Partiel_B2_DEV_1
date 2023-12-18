<?php

namespace App\Controller;

use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/profile')]
class ProfileController extends AbstractController
{
    /**
     * @param ProfileRepository $profileRepository
     * @return Response
     * Get all profile in app
     */
    #[Route('/all', name: 'app_profile_all')]
    public function index(ProfileRepository $profileRepository): Response
    {
        $profiles = $profileRepository->findAll();

        return $this->json($profiles, 200, [], ["groups"=>"profile:display"]);
    }

    /**
     * @return Response
     * Retrieve all my requests
     */
    #[Route('/myrequests', name: 'app_profile_myrequest')]
    public function myRequest(): Response
    {
        $requests = $this->getUser()->getProfile()->getRequests();

        return $this->json($requests, 200, [], ["groups"=>"requests:display"]);
    }

    /**
     * @return Response
     * Return all the events I am registered for
     */
    #[Route('/events/attending', name: 'app_profile_eventsattending')]
    public function eventsAttending(): Response
    {
        $events = $this->getUser()->getProfile()->getEventsAttending();
        return $this->json($events, 200, [], ["groups"=>"events:attending:display"]);
    }
}
