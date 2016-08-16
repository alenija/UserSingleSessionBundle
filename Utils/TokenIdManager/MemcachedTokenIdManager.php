<?php

namespace Requestum\UserSingleSessionBundle\Utils\TokenIdManager;


use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MemcachedTokenIdManager implements TokenIdManagerInterface
{

    /**
     * @var string
     */
    private $keyPrefix = 'login-instance-id-';

    /**
     * @var \Memcached
     */
    protected $memcached;

    public function __construct(\Memcached $memcached)
    {
        $this->memcached = $memcached;
    }

    /**
     * @param TokenInterface $token
     */
    public function set(TokenInterface $token)
    {
        $instanceId = $this->memcached->increment($this->generateKey($token->getUser()), 1);

        if ($instanceId >= ((int)floor(PHP_INT_MAX / 2) - 1)) {
            $instanceId = false;
        }

        if (false === $instanceId) {
            $this->memcached->set($this->generateKey($token->getUser()), 1);
        }
    }

    /**
     * @param TokenInterface $token
     * @return mixed
     */
    public function get(TokenInterface $token)
    {
        return $this->memcached->get($this->generateKey($token->getUser()));
    }

    /**
     * @param TokenInterface $token
     */
    public function remove(TokenInterface $token)
    {
        $this->memcached->delete($this->generateKey($token->getUser()));
    }

    private function generateKey(UserInterface $user)
    {
        return $this->keyPrefix . $user->getId();
    }
}