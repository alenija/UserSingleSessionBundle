<?php

namespace Requestum\UserSingleSessionBundle\EventListener;


use Requestum\UserSingleSessionBundle\Utils\TokenValidator;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class TokenValidatorListener
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var TokenValidator
     */
    protected $tokenValidator;

    /**
     * @var Router
     */
    protected $router;

    public function __construct(TokenStorage $tokenStorage, TokenValidator $tokenValidator, Router $router)
    {
        $this->tokenStorage = $tokenStorage;
        $this->tokenValidator = $tokenValidator;
        $this->router = $router;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() === HttpKernelInterface::MASTER_REQUEST) {

            $token = $this->tokenStorage->getToken();

            if ($token && !$this->tokenValidator->validateToken($token)) {
                $response = new RedirectResponse($this->router->generate('logout'));
                $event->setResponse($response);
            }
        };
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->tokenValidator->protectToken($event->getAuthenticationToken());
    }
} 
