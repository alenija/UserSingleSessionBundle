<?php

namespace Requestum\UserSingleSessionBundle\Utils;


use Requestum\UserSingleSessionBundle\Utils\TokenIdManager\TokenIdManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenValidator
{

    /**
     * @var string
     */
    private $tokenAttr = 'instance-usage-count';

    /**
     * @var TokenIdManagerInterface
     */
    private $tokenIdManager;

    /**
     * TokenValidator constructor.
     * @param TokenIdManagerInterface $tokenIdManager
     */
    public function __construct(TokenIdManagerInterface $tokenIdManager)
    {
        $this->tokenIdManager = $tokenIdManager;
    }

    public function validateToken(TokenInterface $token)
    {
        if (!$this->isValidationNeeded($token) || !$this->isSupports($token)) {
            return true;
        }

        if (!$token->hasAttribute($this->tokenAttr))
        {
            return false;
        }

        /** instance absent or pushed from storage - protect token */
        if (!($storedTokenUsageCount = $this->tokenIdManager->get($token))) {
            $this->protectToken($token);
            return true;
        }

        $currentTokenUsageCount = $token->getAttribute($this->tokenAttr);

        return $storedTokenUsageCount === $currentTokenUsageCount;
    }

    public function protectToken(TokenInterface $token)
    {
        if ($this->isValidationNeeded($token) || $this->isSupports($token)) {
            $this->tokenIdManager->set($token);
            $token->setAttribute($this->tokenAttr, $this->tokenIdManager->get($token));
        }
    }

    private function isValidationNeeded(TokenInterface $token)
    {
        return $token instanceof UsernamePasswordToken || $token instanceof RememberMeToken;
    }

    /**
     * @param TokenInterface $token
     * @return bool
     */
    private function isSupports(TokenInterface $token)
    {
        return $token->getUser() instanceof UserInterface;
    }
} 
