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

    /**
     * Array of API routes that don't need token
     */
    private $apiRoutesTokenException = [
        '/api/login',
        '/api/login_check',
        '/api/login_google',
        '/api/doc',
        '/api/v1/users/create',
        '/api/v1/users/google_update',
        '/api/v1/sales',
        '/api/v1/reset-password'
    ];

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function supports(Request $request): ?bool
    {

        return !in_array($request->getPathInfo(), $this->apiRoutesTokenException);
                
    }

    public function authenticate(Request $request): Passport
    {
        $auth = $request->headers->get(('Authorization'));
        $apiToken = substr($auth, 7);

        if (!$apiToken) {
            throw new CustomUserMessageAuthenticationException('Token not found in headers');
        }

        if ($request->getPathInfo() === '/api/v1/update-password') {
            return new SelfValidatingPassport(
                new UserBadge($apiToken, function($apiToken) {
                    $user = $this->userRepository->findOneBy(['tempApiToken' => $apiToken]);
                    
                    if (!$user) {
                        throw new CustomUserMessageAuthenticationException('No user found (temp)');
                    }
                     return $user;
                })
            );
        } else {
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
}
