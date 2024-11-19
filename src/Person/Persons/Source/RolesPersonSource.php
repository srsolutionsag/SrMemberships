<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Person\Persons\Source;

use Generator;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class RolesPersonSource implements PersonSource
{
    public function __construct(protected array $role_ids, private readonly \ilRbacReview $rbac_review)
    {
    }

    /**
     * @return Generator|RawPerson[]
     */
    public function getRawEntries(): Generator
    {
        foreach ($this->role_ids as $role_id) {
            foreach ($this->rbac_review->assignedUsers((int) $role_id) as $assigned_user) {
                yield new RawPerson((string) $assigned_user);
            }
        }
    }
}
