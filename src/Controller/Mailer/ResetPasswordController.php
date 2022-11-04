<?php

namespace App\Controller\Mailer;

use App\Repository\UserRepository;
use App\Utils\CustomMailer;
use App\Utils\GetBaseUrl;
use App\Utils\TokenCreator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/password", name="app_mailer_password")
 */
class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/reset", name="_reset", methods="GET")
     */
    public function reset(
        Request $request,
        UserRepository $userRepository,
        CustomMailer $mailer,
        GetBaseUrl $baseUrl,
        EntityManagerInterface $em,
        TokenCreator $tokenCreator,
        UserPasswordHasherInterface $passwordHash
        ): Response
    {
        if($request->query->get('email') && $request->query->get('token')) {
            $email = filter_var($request->query->get('email'), FILTER_VALIDATE_EMAIL);
            $token = htmlspecialchars($request->query->get('token'));

            $user = $userRepository->findOneBy(['email' => $email, 'tempToken' => $token]);

            if ($user !== null) {
                
                $currentDateTime = new \DateTimeImmutable();

                if ($currentDateTime->getTimestamp() - $user->getTempTokenUpdatedAt()->getTimeStamp() >= $mailer->getMailExpiredTime()) {
                    // If tempToken expires, delete tempToken and redirect to error message page

                    $user->setTempToken(null);
                    $user->setTempTokenUpdatedAt(null);

                    $em->flush();

                    return $this->redirect($baseUrl->getMainUrl() . '/oubli-mdp?error=expired');
                } else {

                    $user->setTempApiToken(
                        $tokenCreator->create(
                            $user->getEmail(), $passwordHash->hashPassword(
                                $user, $user->getPassword() ?? $user->getSub()
                            )
                        )
                    );

                    $em->flush();

                    return $this->redirect($baseUrl->getMainUrl() . '/oubli-mdp?token=' . $user->getTempApiToken());
                }

            }
        }

        return $this->redirect($baseUrl->getMainUrl() . '/oubli-mdp?error=not-found');
    }
}
