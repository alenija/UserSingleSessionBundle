<?php

namespace Requestum\UserSingleSessionBundle\Utils;


interface SingleSessionUserInterface
{
    /**
     * Setting single session token usages count to the entity.
     *
     * @param int $value
     */
    public function setSingleSessionTokenUsageCount($value);

    /**
     * Must return number or null values. Return null if value is not set.
     *
     * @return int|null
     */
    public function getSingleSessionTokenUsageCount();
}