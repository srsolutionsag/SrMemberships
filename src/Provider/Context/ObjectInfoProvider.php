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

use ilTree;
use ilCtrl;
use Psr\Http\Message\ServerRequestInterface;
use ilRbacReview;
use ilCourseMembershipGUI;
use ilRepositoryGUI;
use ilObjCourseGUI;
use ilGroupMembershipGUI;
use ilObjGroupGUI;
use ilObject2;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ObjectInfoProvider
{
    public const TYPE_CRS = 'crs';
    public const TYPE_GRP = 'grp';
    private array $cache = [];

    /**
     * @var string[]
     */
    private array $valid_parent_types = ['crs', 'grp', 'root', 'cat'];

    public function __construct(private readonly \ilTree $tree, private readonly \ilCtrl $ctrl, private readonly ServerRequestInterface $request, private readonly \ilRbacReview $rbacreview)
    {
    }

    public function getType(int $ref_id): string
    {
        if (isset($this->cache[$ref_id])) {
            return $this->cache[$ref_id];
        }

        $node_info = $this->tree->getNodeData($ref_id);
        $native_type = $node_info['type'] ?? null;
        $this->cache[$ref_id] = match ($native_type) {
            'crs' => self::TYPE_CRS,
            'grp' => self::TYPE_GRP,
            default => $native_type ?? 'unknown',
        };

        return $this->cache[$ref_id];
    }

    public function getMembersTabLink(int $ref_id): string
    {
        $type = $this->getType($ref_id);
        switch ($type) {
            case self::TYPE_CRS:
                $this->ctrl->setParameterByClass(ilCourseMembershipGUI::class, 'ref_id', $ref_id);
                return $this->ctrl->getLinkTargetByClass(
                    [ilRepositoryGUI::class, ilObjCourseGUI::class, ilCourseMembershipGUI::class]
                );
            case self::TYPE_GRP:
                $this->ctrl->setParameterByClass(ilGroupMembershipGUI::class, 'ref_id', $ref_id);
                return $this->ctrl->getLinkTargetByClass(
                    [ilRepositoryGUI::class, ilObjGroupGUI::class, ilGroupMembershipGUI::class]
                );
            default:
                return 'unknown';
        }
    }

    public function isOnMembersTab(int $ref_id): bool
    {
        $command_class = $this->request->getQueryParams()['cmdClass'] ?? '';
        return match ($this->getType($ref_id)) {
            ObjectInfoProvider::TYPE_CRS => strtolower((string) $command_class) === strtolower(ilCourseMembershipGUI::class),
            ObjectInfoProvider::TYPE_GRP => strtolower((string) $command_class) === strtolower(ilGroupMembershipGUI::class),
            default => false,
        };
    }

    /**
     * @param array $role_ids of int
     * @return array int => string
     */
    public function translateRoleIds(array $role_ids): array
    {
        $roles = [];
        foreach ($role_ids as $role_id) {
            $role_id = (int) $role_id;
            $roles[$role_id] = ilObject2::_lookupTitle($role_id);
        }
        return $roles;
    }

    public function getGlobalAndLocalRoles(): array
    {
        return $this->getGlobalRoles() + $this->getLocalRoles();
    }

    public function getGlobalRoles(): array
    {
        $roles = [];
        foreach ($this->rbacreview->getRolesByFilter(ilRbacReview::FILTER_ALL_GLOBAL) as $role) {
            $role_id = (int) $role['obj_id'];
            if ($role_id === 14) {
                continue;
            }
            $roles[$role_id] = $role['title'];
        }

        return $roles;
    }

    public function getLocalRoles(): array
    {
        $roles = [];
        foreach ($this->rbacreview->getRolesByFilter(ilRbacReview::FILTER_NOT_INTERNAL) as $role) {
            $parent = $this->tree->getNodeData($role['parent'] ?? 0);
            $parent_type = $parent['type'] ?? '';
            if (!in_array($parent_type, $this->valid_parent_types, true)) {
                continue;
            }

            $roles[(int) $role['obj_id']] = $role['title'];
        }

        return $roles;
    }
}
