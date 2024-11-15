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

use ilGroupParticipants;
use ilObject2;
use Generator;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class GroupAccountSource implements AccountSource
{
    /**
     * @readonly
     */
    private \ilGroupParticipants $group_members;

    public function __construct(int $ref_id)
    {
        $this->group_members = new ilGroupParticipants(ilObject2::_lookupObjectId($ref_id));
    }

    public function getRawEntries(): Generator
    {
        yield from $this->group_members->getMembers();
        yield from $this->group_members->getAdmins();
    }

    public function getEntries(): Generator
    {
        yield from array_map(
            static fn ($user_id): RawAccount => new RawAccount((int) $user_id, RawAccount::ROLE_MEMBER),
            $this->group_members->getMembers()
        );
        yield from array_map(
            static fn ($user_id): RawAccount => new RawAccount((int) $user_id, RawAccount::ROLE_ADMIN),
            $this->group_members->getAdmins()
        );
    }
}
