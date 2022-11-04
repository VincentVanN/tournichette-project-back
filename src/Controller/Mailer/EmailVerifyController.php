<?php

namespace App\Controller\Mailer;

use App\Utils\GetBaseUrl;
use App\Utils\CustomMailer;
use App\Utils\TokenCreator;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/email", name="app_mailer_email")
 */
class EmailVerifyController extends AbstractController
{
    /**
     * @Route("/verify", name="_verify", methods="GET")
     */
    public function checkMail(
        Request $request,
        UserRepository $userRepository,
        CustomMailer $mailer,
        GetBaseUrl $baseUrl,
        TokenCreator $tokenCreator,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher): Response
    {
        if($request->query->get('email') && $request->query->get('token')) {
            $email = filter_var($request->query->get('email'), FILTER_VALIDATE_EMAIL);
            $token = htmlspecialchars($request->query->get('token'));

            $user = $userRepository->findOneBy(['email' => $email, 'emailToken' => $token]);

            if ($user !== null) {

                if ($user->isEmailChecked()) {
                    // If Email already checked
                    return $this->redirect($baseUrl->getMainUrl() . '/confirmation-compte?checked=already');
                } else {
                    $currentDateTime = new \DateTimeImmutable();

                    if ($currentDateTime->getTimestamp() - $user->getEmailTokenUpdatedAt()->getTimeStamp() >= $mailer->getMailExpiredTime()) {
                        // If verifying link expired
                        $user->setEmailToken(
                            $tokenCreator->create(
                                $user->getEmail(), $passwordHasher->hashPassword(
                                    $user, $user->getEmail() . $user->getFirstname() . $user->getLastname
                                )));
                        
                        $user->setEmailTokenUpdatedAt($currentDateTime);

                        $em->flush();

                        return $this->redirect($baseUrl->getMainUrl() . '/confirmation-compte?checked=expired');
                    } else {
                        $user->setEmailChecked(true);
                        $user->setEmailToken(null);
                        $user->setEmailTokenUpdatedAt(null);
                        $em->flush();
                        return $this->redirect($baseUrl->getMainUrl() . '/confirmation-compte?checked=checked');
                    }
                }
            }
        }

        return $this->redirect($baseUrl->getMainUrl() . '/confirmation-compte?checked=not-found');
    }
}
