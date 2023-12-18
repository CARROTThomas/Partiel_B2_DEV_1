<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register',methods: "POST")]
    public function register(SerializerInterface $serializer, UserRepository $userRepository, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $manager): Response
    {
        $user = $serializer->deserialize($request->getContent(), User::class, "json");

        $parameters = json_decode($request->getContent(), true);

        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $parameters["password"]
            )
        );

        $exist = $userRepository->findOneBy(['username'=>$user->getUsername()]);

        if ($exist) {
            return $this->json('user exist', 401);
        }

        $profile = new Profile();
        $profile->setDisplayName($user->getUsername);
        $user->setProfile($profile);

        $manager->persist($user);
        $manager->flush();

        return $this->json("user create", 200);
    }
}
