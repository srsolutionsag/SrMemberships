<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
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
    /**
     * @readonly
     */
    private \ilRbacSystem $rbac;
    /**
     * @readonly
     */
    private \ilRbacReview $rbac_review;
    /**
     * @readonly
     */
    private ObjectInfoProvider $object_info_provider;
    private array $cache = [];

    public function __construct(\ilRbacSystem $rbac, \ilRbacReview $rbac_review, ObjectInfoProvider $object_info_provider)
    {
        $this->rbac = $rbac;
        $this->rbac_review = $rbac_review;
        $this->object_info_provider = $object_info_provider;
    }

    public function hasUserPermissionToAdministrate(int $user_id, int $ref_id): bool
    {
        if (isset($this->cache[$user_id][$ref_id])) {
            return $this->cache[$user_id][$ref_id];
        }

        switch ($this->object_info_provider->getType($ref_id)) {
            case ObjectInfoProvider::TYPE_CRS:
            case ObjectInfoProvider::TYPE_GRP:
                $this->cache[$user_id][$ref_id] = $this->rbac->checkAccessOfUser(
                    $user_id,
                    'manage_members',
                    $ref_id
                );
                break;
            default:
                $this->cache[$user_id][$ref_id] = false;
                break;
        }

        return $this->cache[$user_id][$ref_id];
    }

    public function isUserInAtLeastOneRole(int $user_id, array $role_ids): bool
    {
        return $this->rbac_review->isAssignedToAtLeastOneGivenRole($user_id, $role_ids);
    }
}
