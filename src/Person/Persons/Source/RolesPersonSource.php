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

namespace srag\Plugins\SrMemberships\Person\Persons\Source;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class RolesPersonSource implements PersonSource
{
    /**
     * @var \ilRbacReview
     */
    private $rbac_review;
    protected $role_ids = [];

    public function __construct(
        array $role_ids,
        \ilRbacReview $rbac_review
    ) {
        $this->role_ids = $role_ids;
        $this->rbac_review = $rbac_review;
    }

    public function getRawEntries(): \Generator
    {
        foreach ($this->role_ids as $role_id) {
            yield from array_map(function ($user_id) {
                return (int) $user_id;
            }, $this->rbac_review->assignedUsers($role_id));
        }
    }
}
