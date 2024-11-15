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

    private int $user_id;
    private int $internal_role;

    public function __construct(int $user_id, int $internal_role = self::ROLE_NONE)
    {
        $this->user_id = $user_id;
        $this->internal_role = $internal_role;
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
