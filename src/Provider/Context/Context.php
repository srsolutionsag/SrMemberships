<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
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
    protected int $current_ref_id;
    protected int $user_id;
    /**
     * @readonly
     */
    private bool $user_can_administrate;
    /**
     * @readonly
     */
    private string $object_type;
    protected bool $is_cli;

    public function __construct(
        int $current_ref_id,
        int $user_id,
        bool $user_can_administrate,
        string $object_type
    ) {
        $this->current_ref_id = $current_ref_id;
        $this->user_id = $user_id;
        $this->user_can_administrate = $user_can_administrate;
        $this->object_type = $object_type;
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
