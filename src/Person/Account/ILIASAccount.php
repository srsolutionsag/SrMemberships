<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Person\Account;

use srag\Plugins\SrMemberships\Person\Account\Source\RawAccount;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ILIASAccount implements Account
{
    public function __construct(protected int $user_id, protected int $internal_role = RawAccount::ROLE_NONE)
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

    public function setInternalRole(int $internal_role): void
    {
        $this->internal_role = $internal_role;
    }

}
