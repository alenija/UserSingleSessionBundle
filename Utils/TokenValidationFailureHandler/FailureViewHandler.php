<?php

namespace Requestum\UserSingleSessionBundle\Utils\TokenValidationFailureHandler;


use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Templating\EngineInterface;

class FailureViewHandler implements TokenValidationFailureHandlerInterface
{
    /**
     * @var EngineInterface
     */
    private $engine;
    /**
     * @var string
     */
    private $view;
    /**
     * @var TokenStorage
     */
    private $storage;

    /**
     * FailureViewHandler constructor.
     * @param EngineInterface $engine
     * @param TokenStorage $storage
     * @param string $view
     */
    public function __construct(EngineInterface $engine, TokenStorage $storage, $view)
    {
        $this->engine = $engine;
        $this->view = $view;
        $this->storage = $storage;
    }

    /**
     * Handle the token validation failure.
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $token = $this->storage->getToken();
        $this->storage->setToken(null);
        $event->getRequest()->getSession()->invalidate();
        $user = $token->getUser();
        $event->setResponse($this->engine->renderResponse($this->view, [
            'user' => $user
        ]));
    }
}