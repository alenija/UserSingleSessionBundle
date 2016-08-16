<?php

namespace Requestum\UserSingleSessionBundle\Utils\TokenValidationFailureHandler;


use Symfony\Component\HttpKernel\Event\GetResponseEvent;

interface TokenValidationFailureHandlerInterface
{
    /**
     * Handle the token validation failure.
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event);
}