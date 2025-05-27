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

use ilCourseParticipants;
use ilObject2;
use Generator;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class CourseAccountSource implements AccountSource
{
    /**
     * @readonly
     */
    private \ilCourseParticipants $course_memberships;

    public function __construct(int $ref_id)
    {
        $this->course_memberships = new ilCourseParticipants(ilObject2::_lookupObjectId($ref_id));
    }

    public function getRawEntries(): Generator
    {
        yield from $this->course_memberships->getMembers();
        yield from $this->course_memberships->getTutors();
        yield from $this->course_memberships->getAdmins();
    }

    public function getEntries(): Generator
    {
        yield from array_map(
            static fn(int $user_id): RawAccount => new RawAccount($user_id, RawAccount::ROLE_MEMBER),
            array_map('intval', $this->course_memberships->getMembers())
        );
        yield from array_map(
            static fn(int $user_id): RawAccount => new RawAccount($user_id, RawAccount::ROLE_ADMIN),
            array_map('intval', $this->course_memberships->getAdmins())
        );
        yield from array_map(
            static fn(int $user_id): RawAccount => new RawAccount($user_id, RawAccount::ROLE_TUTOR),
            array_map('intval', $this->course_memberships->getTutors())
        );
    }
}
