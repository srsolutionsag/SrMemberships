<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Person\Account\Source;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class RawAccount
{
    public const ROLE_NONE = -1;
    public const ROLE_MEMBER = 1;
    public const ROLE_TUTOR = 2;
    public const ROLE_ADMIN = 3;

    public function __construct(private readonly int $user_id, private readonly int $internal_role = self::ROLE_NONE)
    {
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getInternalRole(): int
    {
        return $this->internal_role;
    }

}
