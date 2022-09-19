<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiAuthenticator extends AbstractAuthenticator
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function supports(Request $request): ?bool
    {
        // $apiRoute = strpos($request->getPathInfo(), '/api/', 0);
        // dd($apiRoute);
        $apiRoute = explode('/', $request->getPathInfo());
        
        $auth = explode(' ', $request->headers->get(('Authorization')));
        // if ($auth[0] == 'Bearer') {
        //     return true;
        // }

        return $auth[0] == 'Bearer' && $apiRoute[1] == 'api';
        
    }

    public function authenticate(Request $request): Passport
    {
        $auth = $request->headers->get(('Authorization'));
        $apiToken = substr($auth, 7);

        if ($apiToken === null) {
            throw new CustomUserMessageAuthenticationException('Token not found in headers');
        }

        return new SelfValidatingPassport(
            new UserBadge($apiToken, function($apiToken) {
                $user = $this->userRepository->findOneBy(['apiToken' => $apiToken]);
                
                if (!$user) {
                    throw new CustomUserMessageAuthenticationException('No user found');
                }
                 return $user;
            })
        );

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

   public function start(Request $request, AuthenticationException $authException = null): Response
   {
       return new JsonResponse('Vous devez être identifier', Response::HTTP_UNAUTHORIZED);
   }
}
