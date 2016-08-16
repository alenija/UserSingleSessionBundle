<?php

namespace Requestum\UserSingleSessionBundle\Utils\TokenIdManager;


use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MemcachedTokenIdManager implements TokenIdManagerInterface
{

    /**
     * @var string
     */
    private $keyPrefix = 'login-instance-usage-count-';

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
        $usageCount = $this->memcached->increment($this->generateKey($token->getUser()), 1);

        if ($usageCount >= ((int)floor(PHP_INT_MAX / 2) - 1)) {
            $usageCount = false;
        }

        if (false === $usageCount) {
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

    private function generateKey(UserInterface $user)
    {
        return $this->keyPrefix . $user->getId();
    }
}