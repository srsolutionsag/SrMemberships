<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Provider\Context;

use ilRbacSystem;
use ilRbacReview;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class UserAccessInfoProvider
{
    private array $cache = [];

    public function __construct(private readonly \ilRbacSystem $rbac, private readonly \ilRbacReview $rbac_review, private readonly ObjectInfoProvider $object_info_provider)
    {
    }

    public function hasUserPermissionToAdministrate(int $user_id, int $ref_id): bool
    {
        if (isset($this->cache[$user_id][$ref_id])) {
            return $this->cache[$user_id][$ref_id];
        }

        $this->cache[$user_id][$ref_id] = match ($this->object_info_provider->getType($ref_id)) {
            ObjectInfoProvider::TYPE_CRS, ObjectInfoProvider::TYPE_GRP => $this->rbac->checkAccessOfUser(
                $user_id,
                'manage_members',
                $ref_id
            ),
            default => false,
        };

        return $this->cache[$user_id][$ref_id];
    }

    public function isUserInAtLeastOneRole(int $user_id, array $role_ids): bool
    {
        return $this->rbac_review->isAssignedToAtLeastOneGivenRole($user_id, $role_ids);
    }
}
