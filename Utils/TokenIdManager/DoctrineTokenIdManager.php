<?php

namespace Requestum\UserSingleSessionBundle\Utils\TokenIdManager;


use Doctrine\ORM\EntityManager;
use Requestum\UserSingleSessionBundle\Utils\SingleSessionUserInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class DoctrineTokenIdManager implements TokenIdManagerInterface
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * DoctrineTokenIdManager constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param TokenInterface $token
     */
    public function set(TokenInterface $token)
    {
        $user = $token->getUser();
        $this->validateUser($user);
        /** @var SingleSessionUserInterface $user */
        $user->setSingleSessionTokenUsageCount(
            null === ($count = $user->getSingleSessionTokenUsageCount()) ? 1 : $count + 1
        );
        $this->manager->persist($user);
        $this->manager->flush();
    }

    /**
     * @param TokenInterface $token
     * @return mixed
     */
    public function get(TokenInterface $token)
    {
        return $token->getUser()->getSingleSessionTokenUsageCount();
    }

    private function validateUser(UserInterface $user)
    {
        if (!is_a($user, SingleSessionUserInterface::class, true)) {
            throw new BadRequestHttpException(
                sprintf("Authorized user must implements %s interface", SingleSessionUserInterface::class)
            );
        }
    }
}