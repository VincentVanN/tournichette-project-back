<?php

namespace App\Security;

use DateTime;
use App\Utils\TokenCreator;
use App\Utils\CreateGoogleUser;
use App\Repository\UserRepository;
use App\Controller\Api\UserController;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

/**
 * This class authenticate the User with Google Sub send by front.
 * If the user exists in bdd, redirect to api_login route (which return apiToken).
 * If the user doesn't exists, it create one and return the apiToken.
 */
class ApiGoogleAuthenticator extends AbstractAuthenticator
{
    private $userRepository;
    private $jsonRequest;
    private $createGoogleUser;
    private $tokenCreator;
    private $urlGenerator;

    public function __construct(UserRepository $userRepository, CreateGoogleUser $createGoogleUser, TokenCreator $tokenCreator, UrlGeneratorInterface $urlGenerator)
    {
        $this->userRepository = $userRepository;
        $this->createGoogleUser = $createGoogleUser;
        $this->tokenCreator = $tokenCreator;
        $this->urlGenerator = $urlGenerator;
    }
    public function supports(Request $request): ?bool
    {
        return $request->getPathinfo() == '/api/login_google';
    }

    public function authenticate(Request $request): Passport
    {
        // dd('ici');
        $this->jsonRequest = $request;
        $arrayRequest = $request->toArray();

        if (!isset($arrayRequest['sub'])) {
            throw new CustomUserMessageAuthenticationException('\'Sub\' can\'t be empty', [], Response::HTTP_BAD_REQUEST);
        }

        $sub = $arrayRequest['sub'];

        return new SelfValidatingPassport(
            new UserBadge($sub, function($sub) {
                $user = $this->userRepository->findOneBy(['sub' => $sub]);
                
                if (!$user) {
                    $user = $this->createGoogleUser->create($this->jsonRequest);
                    if(!$user instanceof User) {
                        throw new CustomUserMessageAuthenticationException('Erreur lors de la création du compte', $user, Response::HTTP_BAD_REQUEST);
                    }
                }
                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // $user = $token->getUser();
        
        $currentDateTime = new DateTime();
        $user = $token->getUser();

        // A custom token is created if not exists or if it's expired
        if (($user->getApiToken() === null) || ($currentDateTime->getTimestamp() - $user->getApiTokenUpdatedAt()->getTimeStamp() >= $this->tokenCreator->getTokenExpiredTime())) {
            $user->setApiToken($this->tokenCreator->create($user->getUserIdentifier(), $user->getSub()));
            $this->userRepository->add($user, true);
        }

        return new JsonResponse([
            'token' => $user->getApiToken()
        ]);
        // return new JsonResponse(['token' => $token->getUser()->getApiToken()], Response::HTTP_ACCEPTED);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['message' => $exception->getMessageKey(), 'data' => $exception->getMessageData()], $exception->getCode());
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}
