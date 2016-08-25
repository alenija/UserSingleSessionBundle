<?php

namespace Requestum\UserSingleSessionBundle\EventListener;


use Requestum\UserSingleSessionBundle\Utils\TokenValidationFailureHandler\TokenValidationFailureHandlerInterface;
use Requestum\UserSingleSessionBundle\Utils\TokenValidator;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
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
     * @var TokenValidationFailureHandlerInterface
     */
    private $validationFailureHandler;

    /**
     * TokenValidatorListener constructor.
     * @param TokenStorage $tokenStorage
     * @param TokenValidator $tokenValidator
     * @param TokenValidationFailureHandlerInterface $validationFailureHandler
     */
    public function __construct(TokenStorage $tokenStorage, TokenValidator $tokenValidator, TokenValidationFailureHandlerInterface $validationFailureHandler)
    {
        $this->tokenStorage = $tokenStorage;
        $this->tokenValidator = $tokenValidator;
        $this->validationFailureHandler = $validationFailureHandler;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() === HttpKernelInterface::MASTER_REQUEST) {

            $token = $this->tokenStorage->getToken();

            if ($token && !$this->tokenValidator->validateToken($token)) {
                $this->validationFailureHandler->handle($event);
            }
        };
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->tokenValidator->protectToken($event->getAuthenticationToken());
    }
} 
