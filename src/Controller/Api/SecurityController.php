<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Utils\TokenCreator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController
{
    /**
     * This is the login route API. Return a custom token.
     * 
     * @Route("/api/login_check", name="api_login", methods="POST")
     * 
     * @var User $user
     */
    public function JsonLogin(TokenCreator $tokenCreator, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($user === null) {
            return $this->json(['message' => 'Identifiants incorrects'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->isEmailChecked()) {
            return $this->json(['message' => 'Ton compte n\'est pas activé, clique sur le lien dans l\'email d\'activation du compte.'], Response::HTTP_UNAUTHORIZED);
        }

        $currentDateTime = new DateTime();

        // A custom token is created if not exists or if it's expired
        if (($user->getApiToken() === null) || ($currentDateTime->getTimestamp() - $user->getApiTokenUpdatedAt()->getTimeStamp() >= $tokenCreator->getTokenExpiredTime())) {
            $user->setApiToken($tokenCreator->create($user->getUserIdentifier(), $user->getPassword()));
            $em->flush();
        }

        return $this->json([
            'token' => $user->getApiToken()
        ]);
    }

    /**
     * This is the login route API with Google Account. Return a custom token.
     * 
     * @Route("/api/login_google", name="google_login", methods="POST")
     * 
     * @var User $user
     */
    public function GoogleLogin(TokenCreator $tokenCreator, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($user === null) {
            return $this->json(['message' => 'Identifiants incorrects'], Response::HTTP_UNAUTHORIZED);
        }

        $currentDateTime = new DateTime();

        // A custom token is created if not exists or if it's expired
        if (($user->getApiToken() === null) || ($currentDateTime->getTimestamp() - $user->getApiTokenUpdatedAt()->getTimeStamp() >= $tokenCreator->getTokenExpiredTime())) {
            $user->setApiToken($tokenCreator->create($user->getUserIdentifier(), $user->getSub()));
            $em->flush();
        }

        return $this->json([
            'token' => $user->getApiToken()
        ]);
    }
}
