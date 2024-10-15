<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Action\Helpers;

use ilGroupParticipants;
use ilObject2;
use srag\Plugins\SrMemberships\Person\Account\Account;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
trait GroupMembers
{
    private int $group_ref_id;

    private ilGroupParticipants $group_members;

    private int $member_role_id;

    public function __construct(int $group_ref_id)
    {
        $this->group_ref_id = $group_ref_id;
        $this->group_members = new ilGroupParticipants(ilObject2::_lookupObjectId($this->group_ref_id));
        $this->member_role_id = $this->resolveMemberRoleId();
    }

    protected function addToContainer(Account $account): void
    {
        if ($this->group_members->isAssigned($account->getUserId())) {
            return;
        }

        $this->group_members->add(
            $account->getUserId(),
            $this->member_role_id
        );
    }

    protected function removeFromContainer(Account $account): void
    {
        $this->group_members->delete($account->getUserId());
    }

    protected function resolveMemberRoleId(): int
    {
        /** @noinspection PhpUndefinedClassConstantInspection */
        return defined('IL_GRP_MEMBER') ? \IL_GRP_MEMBER : ilGroupParticipants::IL_GRP_MEMBER;
    }
}
