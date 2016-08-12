<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 4/17/14
 * Time: 12:45 PM
 */

namespace UserBundle\Service;


use UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TokenValidator
{

    private $keyPrefix = 'login-instance-id-';
    private $tokenAttr = 'instance-id';
    private $expiration = 0;

    protected $memcached;

    public function __construct(\Memcached $memcached)
    {
        $this->memcached = $memcached;
    }

    public function validateToken(TokenInterface $token)
    {
        if (!$this->needValidation($token) || !$this->supports($token)) {
            return true;
        }

        /** don't have attribute - invalid */
        if (!$token->hasAttribute($this->tokenAttr)) {
            return false;
        }

        $tokenId = $token->getAttribute($this->tokenAttr);

        /** instance absent or pushed from storage - protect token */
        if (!($storedId = $this->getTokenId($token))) {
            $this->protectToken($token);
            return true;
        }

        return $storedId === $tokenId;
    }

    public function protectToken(TokenInterface $token)
    {
        if (!$this->needValidation($token) || !$this->supports($token)) {
            return true;
        }

        $token->setAttribute($this->tokenAttr, $this->instantiateTokenId($token));
    }

    protected function getTokenId(TokenInterface $token)
    {
        return $this->memcached->get($this->generateKey($token->getUser()));
    }

    protected function instantiateTokenId(TokenInterface $token)
    {
        $instanceId = $this->memcached->increment($this->generateKey($token->getUser()), 1);

        if ($instanceId >= ((int) floor(PHP_INT_MAX / 2) - 1)) {
            $instanceId = false;
        }

        if (false === $instanceId) {
            $this->memcached->set($this->generateKey($token->getUser()), 1);
            $instanceId = 1;
        }

        return $instanceId;
    }

    private function needValidation(TokenInterface $token)
    {
        return $token instanceof UsernamePasswordToken || $token instanceof RememberMeToken;
    }

    private function generateKey(User $user)
    {
        return $this->keyPrefix . $user->getId();
    }

    private function supports(TokenInterface $token)
    {
        return $token->getUser() instanceof User;
    }
} 
