<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function show(UserRepository $userRepository, SerializerInterface $serializer) :Response
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
        UserRepository $userRepository): Response
    {
        $data = $request->getContent();

        $user = $serializer->deserialize($data, User::class, 'json');
        $hashPassword = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashPassword);
        $user->setRoles(["ROLE_USER"]);

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            // $errorString = (string) $errors;
            // dd($errors);
            $data = [];
            foreach($errors as $currentError) {
                $data[$currentError->getPropertyPath()] = $currentError->getMessage();
                
            }
            return $this->prepareResponse('Email déjà existant', [], ['data' => $data], true, Response::HTTP_BAD_REQUEST);
        }

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
        // $currentPassword = $user->getPassword();

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