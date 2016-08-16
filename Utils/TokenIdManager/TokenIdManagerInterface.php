<?php

namespace Requestum\UserSingleSessionBundle\Utils\TokenIdManager;


use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

interface TokenIdManagerInterface
{

    /**
     * @param TokenInterface $token
     */
    public function set(TokenInterface $token);

    /**
     * @param TokenInterface $token
     * @return mixed
     */
    public function get(TokenInterface $token);
}