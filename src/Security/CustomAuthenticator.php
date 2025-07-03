<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * @see https://symfony.com/doc/current/security/custom_authenticator.html
 */
class CustomAuthenticator extends AbstractAuthenticator
{
    private UserRepository $userRepository;
    private RouterInterface $router;
    public function __construct(UserRepository $userRepository, RouterInterface $router)
    {
        $this->userRepository = $userRepository;
        $this->router = $router;

    }

    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() === '/login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('_username');
        $password = $request->request->get('_password');

        return new Passport(
            new UserBadge($email, function($userIdentifier){
                $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);

                if(!$user){
                    throw new UserNotFoundException();
                }
                return $user;
            }),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $roles = $token->getRoleNames();

        if (in_array('ROLE_ADMIN', $roles, true))
        {
            return new RedirectResponse($this->router->generate('app_admin'));
        }
        if (in_array('ROLE_USER', $roles, true))
        {
            return new RedirectResponse($this->router->generate('app_customer'));
        }
        return new RedirectResponse($this->router->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
       $message = strtr($exception->getMessageKey(), $exception->getMessageData());

       $request->getSession()->set('error', $message);
       return new RedirectResponse($this->router->generate('app_login'));
    }

     public function start(Request $request, ?AuthenticationException $authException = null): Response
     {
         return new RedirectResponse($this->router->generate('app_login'));
     }
}
