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
    /**
     * @readonly
     */
    private ServerRequestInterface $request;
    public const TYPE_CRS = 'crs';
    public const TYPE_GRP = 'grp';
    /**
     * @var \ilTree
     */
    private $tree;
    private array $cache = [];
    /**
     * @var \ilCtrl
     */
    private $ctrl;
    /**
     * @var \ilRbacReview
     */
    private $rbacreview;

    /**
     * @var string[]
     */
    private array $valid_parent_types = ['crs', 'grp', 'root', 'cat'];

    public function __construct(
        ilTree $tree,
        ilCtrl $ctrl,
        ServerRequestInterface $request,
        ilRbacReview $rbac_review
    ) {
        $this->request = $request;
        $this->tree = $tree;
        $this->ctrl = $ctrl;
        $this->rbacreview = $rbac_review;
    }

    public function getType(int $ref_id): string
    {
        if (isset($this->cache[$ref_id])) {
            return $this->cache[$ref_id];
        }

        $node_info = $this->tree->getNodeData($ref_id);
        $native_type = $node_info['type'] ?? null;
        switch ($native_type) {
            case 'crs':
                $this->cache[$ref_id] = self::TYPE_CRS;
                break;
            case 'grp':
                $this->cache[$ref_id] = self::TYPE_GRP;
                break;
            default:
                $this->cache[$ref_id] = $native_type ?? 'unknown';
                break;
        }

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
        switch ($this->getType($ref_id)) {
            case ObjectInfoProvider::TYPE_CRS:
                return strtolower((string) $command_class) === strtolower(ilCourseMembershipGUI::class);
            case ObjectInfoProvider::TYPE_GRP:
                return strtolower((string) $command_class) === strtolower(ilGroupMembershipGUI::class);
            default:
                return false;
        }
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
