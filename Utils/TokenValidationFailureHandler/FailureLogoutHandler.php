<?php

namespace Requestum\UserSingleSessionBundle\Utils\TokenValidationFailureHandler;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;

class FailureLogoutHandler implements TokenValidationFailureHandlerInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * LogoutHandler constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Handle the token validation failure.
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $response = new RedirectResponse($this->router->generate('logout'));
        $event->setResponse($response);
    }
}