<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 *
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Action\Helpers;

use srag\Plugins\SrMemberships\Person\Account\Account;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
trait GroupMembers
{
    /**
     * @var int
     */
    private $group_ref_id;
    /**
     * @var \ilGroupParticipants
     */
    private $group_members;
    /**
     * @var int
     */
    private $member_role_id;

    public function __construct(int $group_ref_id)
    {
        $this->group_ref_id = $group_ref_id;
        $this->group_members = new \ilGroupParticipants(\ilObject2::_lookupObjectId($this->group_ref_id));
        $this->member_role_id = $this->resolveMemberRoleId();
    }

    protected function addToContainer(Account $account): void
    {
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
        return defined('IL_GRP_MEMBER') ? \IL_GRP_MEMBER : \ilGroupParticipants::IL_GRP_MEMBER;
    }
}
