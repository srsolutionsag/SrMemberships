<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\ByRoleSync\Config;

use srag\Plugins\SrMemberships\Workflow\Config\AbstractDBWorkflowConfig;
use srag\Plugins\SrMemberships\Provider\Context\ObjectInfoProvider;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ByRoleSyncConfig extends AbstractDBWorkflowConfig
{
    public const F_SELECTABLE_GLOBAL_ROLES = 'selectable_global_roles';
    public const F_OFFER_WORKFLOW_TO = 'offer_workflow_to';
    public const F_SELECTABLE_LOCAL_ROLES = 'selectable_local_roles';

    public function getNameSpace(): string
    {
        return 'by_role_sync';
    }

    public function getAvailableRolesForSelection(ObjectInfoProvider $info): array
    {
        $global_roles = $this->get(ByRoleSyncConfig::F_SELECTABLE_GLOBAL_ROLES) ?? [];
        $global_roles = $global_roles === [-1] ? $info->getGlobalRoles() : $info->translateRoleIds($global_roles);

        $local_roles = $this->get(ByRoleSyncConfig::F_SELECTABLE_LOCAL_ROLES) ?? [];
        $local_roles = $local_roles === [-1] ? $info->getLocalRoles() : $info->translateRoleIds($local_roles);
        return $global_roles + $local_roles;
    }
}
