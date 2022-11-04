<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use App\Utils\CustomMailer;
use App\Utils\TokenCreator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/** 
 * @Route("/api/v1", name="api_v1" )
 *  
 */
class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/reset-password", name="_reset-password", methods={"POST"})
     */
    public function reset(
        Request $request,
        CustomMailer $mailer,
        UserRepository $userRepository,
        TokenCreator $tokenCreator,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
        ): Response
    {
        $requestData = \json_decode($request->getContent(), true);

        $email = filter_var($requestData['email'], FILTER_VALIDATE_EMAIL);

        if (!$email) {
            return $this->json(['message' => 'l\'email n\'a pas la bonne forme'], Response::HTTP_BAD_REQUEST);
        } else {
            $user = $userRepository->findOneBy(['email' => $email]);
            
            if ($user !== null) {

                $user->setTempToken(
                    $tokenCreator->create(
                        $user->getEmail(), $passwordHasher->hashPassword($user, $user->getPassword() ?? $user->getSub())
                    ));

                $user->setTempTokenUpdatedAt(new \DateTimeImmutable());
                $em->flush();

                $mailer->emailResetPassword($user);

                return $this->json(['message' => 'Un email vient d\'être envoyé à l\'adresse indiquée'], Response::HTTP_OK);

            } else {
                return $this->json(['message' => 'L\'email n\'existe pas dans notre base de données'], Response::HTTP_NOT_FOUND);
            }
        }

        return $this->json(['message' => 'Une erreur s\'est produite'], 520);
    }

    /**
     * @Route("/update-password", name="_update-password", methods={"PATCH"})
     */
    public function update(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, CustomMailer $mailer): Response
    {
        $user = $this->getUser();
        $requestData = \json_decode($request->getContent(), true);

        if(empty($user)) {
            return $this->json('Pas d\'utilisateur trouvé ou token expiré', Response::HTTP_UNAUTHORIZED);
        }

        if ($user->getTempApiToken() === null) {
            return $this->json('Aucune demande de modification de mot de passe pour cet utilisateur', Response::HTTP_UNAUTHORIZED);   
        }

        if(isset($requestData['password']) && $requestData['password'] != '') {
            $hashPassword = $passwordHasher->hashPassword($user, $requestData['password']);
            $user->setPassword($hashPassword);

            $user->setTempApiToken(null);
            $user->setTempToken(null);
            $user->setTempTokenUpdatedAt(null);

            $em->flush();

            $mailer->emailConfirmResetPassword($user);
            return $this->json('Mot de passe modifié avec succès', Response::HTTP_OK);
        } else {
            return $this->json('Password absent', Response::HTTP_BAD_REQUEST);
        }
    }
}