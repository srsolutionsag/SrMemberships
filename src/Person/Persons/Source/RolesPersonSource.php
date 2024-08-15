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

use ilRbacReview;
use Generator;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class RolesPersonSource implements PersonSource
{
    protected array $role_ids;
    /**
     * @readonly
     */
    private \ilRbacReview $rbac_review;
    public function __construct(array $role_ids, \ilRbacReview $rbac_review)
    {
        $this->role_ids = $role_ids;
        $this->rbac_review = $rbac_review;
    }

    public function getRawEntries(): Generator
    {
        foreach ($this->role_ids as $role_id) {
            yield from array_map(
                fn ($user_id): int => (int) $user_id,
                $this->rbac_review->assignedUsers((int) $role_id)
            );
        }
    }
}
