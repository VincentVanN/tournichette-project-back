<?php

namespace App\Utils;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateGoogleUser
{
    private $serializer;
    private $validator;
    private $userRepository;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, UserRepository $userRepository)
    {
        $this->serialize = $serializer;
        $this->validator = $validator;
        $this->userRepository = $userRepository;
    }
    public function create($jsonRequest)
    {
        $data = $jsonRequest->toArray();
        $user = new User;
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setPhone($data['phone']);
        $user->setEmail($data['email']);
        $user->setSub($data['sub']);
        $user->setRoles(["ROLE_USER"]);

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $data = [];
            foreach($errors as $currentError) {
                $data[$currentError->getPropertyPath()] = $currentError->getMessage();
                
            }
            return $data;
        }

        $this->userRepository->add($user, true);

        return $user;
    }
}