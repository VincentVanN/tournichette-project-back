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
use Symfony\Component\Routing\Annotation\Route;

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
        EntityManagerInterface $em): Response
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
                        $em->flush();
                        return $this->redirect($baseUrl->getMainUrl() . '/confirmation-compte?checked=checked');
                    }
                }
            }
        }

        return $this->redirect($baseUrl->getMainUrl() . '/confirmation-compte?checked=not-found');
    }

    /**
     * @Route("/verify-email", name="_test", methods="GET")
     */
    public function testEmailChecked(Request $request)
    {
        if($request->query->get('checked')) {

            switch ($request->query->get('checked')) {
                case 'expired':
                    dd('lien expiré');
                    break;

                case 'checked':
                    dd('email vérifié');
                    break;

                case 'not-found':
                    dd('utilisateur non trouvé');
                    break;
            }
        } else {
            dd('paramètre absent');
        }

        return;
    }

    /**
     * @Route("/test", name="_test_send", methods="GET")
     */
    public function testEmailVerif(CustomMailer $mailer)
    {
        $mailer->emailVerify($this->getUser());
        dd('mail send');
        return;
    }
}
