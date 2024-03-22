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

use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ObjectInfoProvider
{
    public const TYPE_CRS = 'crs';
    public const TYPE_GRP = 'grp';
    /**
     * @var \ilTree
     */
    private $tree;
    /**
     * @var array
     */
    private $cache = [];
    /**
     * @var \ilCtrl
     */
    private $ctrl;
    /**
     * @var ServerRequestInterface
     */
    private $request;
    /**
     * @var \ilRbacReview
     */
    private $rbacreview;

    /**
     * @var string[]
     */
    private $valid_parent_types;

    public function __construct(
        \ilTree $tree,
        \ilCtrl $ctrl,
        ServerRequestInterface $request,
        \ilRbacReview $rbac_review
    ) {
        $this->tree = $tree;
        $this->ctrl = $ctrl;
        $this->request = $request;
        $this->rbacreview = $rbac_review;
        $this->valid_parent_types = ['crs', 'grp', 'root', 'cat'];
    }

    public function getType(int $ref_id) : string
    {
        if (isset($this->cache[$ref_id])) {
            return $this->cache[$ref_id];
        }

        $node_info = $this->tree->getNodeData($ref_id);
        $native_type = $node_info['type'] ?? null;
        switch ($native_type) {
            case 'crs':
                return $this->cache[$ref_id] = self::TYPE_CRS;
            case 'grp':
                return $this->cache[$ref_id] = self::TYPE_GRP;
            default:
                return $this->cache[$ref_id] = $native_type ?? 'unknown';
        }
    }

    public function getMembersTabLink(int $ref_id) : string
    {
        $type = $this->getType($ref_id);
        switch ($type) {
            case self::TYPE_CRS:
                $this->ctrl->setParameterByClass(\ilCourseMembershipGUI::class, 'ref_id', $ref_id);
                return $this->ctrl->getLinkTargetByClass(
                    [\ilRepositoryGUI::class, \ilObjCourseGUI::class, \ilCourseMembershipGUI::class]
                );
            case self::TYPE_GRP:
                $this->ctrl->setParameterByClass(\ilGroupMembershipGUI::class, 'ref_id', $ref_id);
                return $this->ctrl->getLinkTargetByClass(
                    [\ilRepositoryGUI::class, \ilObjGroupGUI::class, \ilGroupMembershipGUI::class]
                );
            default:
                return 'unknown';
        }
    }

    public function isOnMembersTab(int $ref_id) : bool
    {
        $command_class = $this->request->getQueryParams()['cmdClass'] ?? '';
        switch ($this->getType($ref_id)) {
            case ObjectInfoProvider::TYPE_CRS:
                $context_command_class_fits = strtolower($command_class) === strtolower(\ilCourseMembershipGUI::class);
                break;
            case ObjectInfoProvider::TYPE_GRP:
                $context_command_class_fits = strtolower($command_class) === strtolower(\ilGroupMembershipGUI::class);
                break;
            default:
                $context_command_class_fits = false;
                break;
        }
        return $context_command_class_fits;
    }

    /**
     * @param array $role_ids of int
     * @return array int => string
     */
    public function translateRoleIds(array $role_ids) : array
    {
        $roles = [];
        foreach ($role_ids as $role_id) {
            $role_id = (int) $role_id;
            $roles[$role_id] = \ilObject2::_lookupTitle($role_id);
        }
        return $roles;
    }

    public function getGlobalAndLocalRoles() : array
    {
        return $this->getGlobalRoles() + $this->getLocalRoles();
    }

    public function getGlobalRoles() : array
    {
        $roles = [];
        foreach ($this->rbacreview->getRolesByFilter(\ilRbacReview::FILTER_ALL_GLOBAL) as $role) {
            $role_id = (int) $role['obj_id'];
            if ($role_id === 14) {
                continue;
            }
            $roles[$role_id] = $role['title'];
        }

        return $roles;
    }

    public function getLocalRoles() : array
    {
        $roles = [];
        foreach ($this->rbacreview->getRolesByFilter(\ilRbacReview::FILTER_NOT_INTERNAL) as $role) {
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
