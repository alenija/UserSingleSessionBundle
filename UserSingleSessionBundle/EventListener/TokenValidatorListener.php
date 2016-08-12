<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 4/17/14
 * Time: 11:57 AM
 */

namespace UserBundle\Security\Listener;


use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use UserBundle\Service\TokenValidator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
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
