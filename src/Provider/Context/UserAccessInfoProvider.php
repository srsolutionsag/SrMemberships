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

namespace srag\Plugins\SrMemberships\Provider\Context;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class UserAccessInfoProvider
{

    /**
     * @var \ilRbacSystem
     */
    private $rbac;
    /**
     * @var array
     */
    private $cache = [];
    /**
     * @var ObjectInfoProvider
     */
    private $object_info_provider;
    /**
     * @var \ilRbacReview
     */
    private $rbac_review;

    public function __construct(
        \ilRbacSystem $rbac,
        \ilRbacReview $rbac_review,
        ObjectInfoProvider $object_info_provider
    ) {
        $this->rbac = $rbac;
        $this->object_info_provider = $object_info_provider;
        $this->rbac_review = $rbac_review;
    }

    public function hasUserPermissionToAdministrate(int $user_id, int $ref_id) : bool
    {
        if (isset($this->cache[$user_id][$ref_id])) {
            return $this->cache[$user_id][$ref_id];
        }

        switch ($this->object_info_provider->getType($ref_id)) {
            case ObjectInfoProvider::TYPE_CRS:
            case ObjectInfoProvider::TYPE_GRP:
                return $this->cache[$user_id][$ref_id] = $this->rbac->checkAccessOfUser(
                    $user_id,
                    'manage_members',
                    $ref_id
                );
            default:
                return $this->cache[$user_id][$ref_id] = false;
        }
    }

    public function isUserInAtLeastOneRole(int $user_id, array $role_ids) : bool
    {
        return $this->rbac_review->isAssignedToAtLeastOneGivenRole($user_id, $role_ids);
    }
}
