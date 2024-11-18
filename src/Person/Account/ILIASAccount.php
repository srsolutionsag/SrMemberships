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
    protected int $user_id;
    private int $internal_role = RawAccount::ROLE_NONE;
    protected bool $processed = false;

    public function __construct(int $user_id, int $internal_role = RawAccount::ROLE_NONE)
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

    public function setInternalRole(int $internal_role): self
    {
        $this->internal_role = $internal_role;

        return $this;
    }

    public function setProcessed(bool $status): self
    {
        $this->processed = $status;
        return $this;
    }

    public function hasBeenProcessed(): bool
    {
        return $this->processed;
    }

}
