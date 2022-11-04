<?php

namespace App\Controller\Api;

use DateTime;
use App\Entity\User;
use App\Utils\TokenCreator;
use App\Repository\UserRepository;
use App\Utils\CustomMailer;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/** @Route("/api/v1/users", name="api_v1_users" )
 * 
 */
class UserController extends AbstractController
{

    /**
    * Show current user
    * @Route("", name="_show", methods="GET")
    * @return Response
    */
    public function show(): Response
    {
        $user = $this->getUser();

        if ($user === null)
        {
            return $this->prepareResponse(
                'Cet utilisateur n\'a pas été trouvé',
                [],
                [],
                true,
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->prepareResponse(
            'OK',
            ['groups' => 'api_v1_users_show'],
            ['data' => $user]
        );

    }

    /**
     * Create a user
     * @Route("/create", name="_create", methods="POST")
     */
    public function create(
        Request $request,
        SerializerInterface $serializer,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        UserRepository $userRepository,
        TokenCreator $tokenCreator,
        CustomMailer $mailer): Response
    {
        $data = $request->getContent();

        $user = $serializer->deserialize($data, User::class, 'json');
        if ($user->getPassword() !== null) {
            $hashPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashPassword);
        }
        $user->setRoles(["ROLE_USER"]);

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $data = [];
            foreach($errors as $currentError) {
                $data[$currentError->getPropertyPath()] = $currentError->getMessage();
                
            }
            return $this->prepareResponse('Email déjà existant', [], ['data' => $data], true, Response::HTTP_BAD_REQUEST);
        }

        $user->setEmailNotifications(true);
        
        if ($user->getSub() !== null) {
            $currentDateTime = new DateTime();

            $user->setEmailChecked(true);

            // A custom token is created if not exists or if it's expired
            if (($user->getApiToken() === null) || ($currentDateTime->getTimestamp() - $user->getApiTokenUpdatedAt()->getTimeStamp() >= $tokenCreator->getTokenExpiredTime())) {
                $user->setApiToken($tokenCreator->create($user->getUserIdentifier(), $user->getSub()));
                $userRepository->add($user, true);
            }

            return $this->json([
                'token' => $user->getApiToken()
            ]);
        }

        // Token for mail verif
        $user->setEmailChecked(false);
        $user->setEmailTokenUpdatedAt(new DateTimeImmutable());
        $user->setEmailToken(
            $tokenCreator->create(
                $user->getEmail(), $passwordHasher->hashPassword(
                    $user, $user->getEmail() . $user->getFirstname() . $user->getLastname()
                )));
        $mailer->emailVerify($user);

        $userRepository->add($user, true);

        return $this->prepareResponse('User Create', [], [], false, Response::HTTP_CREATED);
    }

    /**
     * Update a user
     * @Route("/update", name="_update", methods="PATCH")
     */
    public function update(
        Request $request,
        SerializerInterface $serializer,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        UserRepository $userRepository): Response
    {
        $data = $request->getContent();
        $user = $this->getUser();

        $requestData = \json_decode($request->getContent(), true);

        if(empty($user)) {
            return $this->prepareResponse(
                'Pas d\'utilisateur trouvé ou token expiré',
                [],
                [],
                true,
                Response::HTTP_UNAUTHORIZED
            );
        }

        if (!isset($requestData['currentpassword'])) {
            return $this->prepareResponse(
                'Veuillez entrer votre mot de passe',
                [],
                [],
                true,
                Response::HTTP_BAD_REQUEST
            );
        }
        
        if (!$passwordHasher->isPasswordValid($user, $requestData['currentpassword'])) {
            return $this->prepareResponse(
                'Mot de passe invalide',
                [],
                [],
                true,
                Response::HTTP_BAD_REQUEST
            );
        }

        $serializer->deserialize($data, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

        if(isset($requestData['password']) && $requestData['password'] != '') {
            $hashPassword = $passwordHasher->hashPassword($user, $requestData['password']);
            $user->setPassword($hashPassword);
        }

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorString = (string) $errors;
            return $this->prepareResponse($errorString, [], [], true, Response::HTTP_BAD_REQUEST);
        }

        $em->flush();

        return $this->prepareResponse('User edited', [], [], false, Response::HTTP_OK);
    }

    /**
     * Update notifications choice of a user
     * @Route("/notifications", name="_notifications", methods="PATCH")
     */
    public function updateNotifications(Request $request, EntityManagerInterface $em)
    {
        $data = $request->getContent();
        $user = $this->getUser();

        $requestData = \json_decode($request->getContent(), true);

        if (!isset($requestData['emailNotifications'])) {
            return $this->prepareResponse(
                'emailNotifications est absent',
                [],
                [],
                true,
                Response::HTTP_BAD_REQUEST
            );
        }

        if (isset($requestData['emailNotifications']) && !is_bool($requestData['emailNotifications'])) {
            return $this->prepareResponse(
                'emailNotifications doit être un booléen',
                [],
                [],
                true,
                Response::HTTP_BAD_REQUEST
            );
        }

        $user->setEmailNotifications($requestData['emailNotifications']);

        $em->flush();

        return $this->prepareResponse('Notifications enregistrées', [], [], false, Response::HTTP_OK);
    }

    /**
     * Update a user with sub Google account
     * @Route("/google_update", name="_update_google", methods="PATCH")
     */
    public function googleUpdate(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        TokenCreator $tokenCreator,
        EntityManagerInterface $em,
        UserRepository $userRepository): Response
    {
        $data = $request->getContent();

        $requestData = \json_decode($request->getContent(), true);

        if (!isset($requestData['password'])) {
            return $this->prepareResponse(
                'Veuillez entrer votre mot de passe',
                [],
                [],
                true,
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!isset($requestData['sub'])) {
            return $this->prepareResponse(
                'Le sub Google doit être renseigné',
                [],
                [],
                true,
                Response::HTTP_BAD_REQUEST
            );
        }

        $user = $userRepository->findOneBy(['email' => $requestData['email']]);

        if(empty($user)) {
            return $this->prepareResponse(
                'Pas d\'utilisateur trouvé',
                [],
                [],
                true,
                Response::HTTP_UNAUTHORIZED
            );
        }
        
        if (!$passwordHasher->isPasswordValid($user, $requestData['password'])) {
            return $this->prepareResponse(
                'Mot de passe invalide',
                [],
                [],
                true,
                Response::HTTP_BAD_REQUEST
            );
        }

        $user->setSub($requestData['sub']);

        $currentDateTime = new DateTime();

        // A custom token is created if not exists or if it's expired
        if (($user->getApiToken() === null) || ($currentDateTime->getTimestamp() - $user->getApiTokenUpdatedAt()->getTimeStamp() >= $tokenCreator->getTokenExpiredTime())) {
            $user->setApiToken($tokenCreator->create($user->getUserIdentifier(), $user->getPassword()));
            $em->flush();
        }

        $em->flush();



        return $this->json([
            'token' => $user->getApiToken()
        ]);
    }

    private function prepareResponse(
        string $message, 
        array $options = [], 
        array $data = [], 
        bool $isError = false, 
        int $httpCode = 200, 
        array $headers = []
    )
    {
        $responseData = [
            'error' => $isError,
            'message' => $message,
        ];

        foreach ($data as $key => $value)
        {
            $responseData[$key] = $value;
        }
        return $this->json($responseData, $httpCode, $headers, $options);
    }
    
}