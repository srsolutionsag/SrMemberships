<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Provider\Context;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class Context
{
    protected bool $is_cli;

    public function __construct(
        protected int $current_ref_id,
        protected int $user_id,
        private readonly bool $user_can_administrate,
        private readonly string $object_type
    ) {
        $this->is_cli = (PHP_SAPI === 'cli');
    }

    public function getCurrentRefId(): int
    {
        return $this->current_ref_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @return string|null a valid context type such as
     */
    public function getContextType(): string
    {
        return $this->object_type;
    }

    public function canUserAdministrateMembers(): bool
    {
        return $this->user_can_administrate;
    }

    public function isCli(): bool
    {
        return $this->is_cli;
    }
}
