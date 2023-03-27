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

namespace srag\Plugins\SrMemberships\Workflow\ByRoleSync\Config;

use srag\Plugins\SrMemberships\Workflow\Config\AbstractDBWorkflowConfig;
use srag\Plugins\SrMemberships\Workflow\Config\WorkflowConfig;
use srag\Plugins\SrMemberships\Provider\Context\ObjectInfoProvider;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ByRoleSyncConfig extends AbstractDBWorkflowConfig implements WorkflowConfig
{
    public const F_SELECTABLE_GLOBAL_ROLES = 'selectable_global_roles';
    public const F_SELECTABLE_LOCAL_ROLES = 'selectable_local_roles';

    public function getNameSpace(): string
    {
        return 'by_role_sync';
    }

    public function getAvailableRolesForSelection(ObjectInfoProvider $info): array
    {
        $global_roles = $this->get(ByRoleSyncConfig::F_SELECTABLE_GLOBAL_ROLES) ?? [];
        if ($global_roles === [-1]) {
            $global_roles = $info->getGlobalRoles();
        } else {
            $global_roles = $info->translateRoleIds($global_roles);
        }

        $local_roles = $this->get(ByRoleSyncConfig::F_SELECTABLE_LOCAL_ROLES) ?? [];
        if ($local_roles === [-1]) {
            $local_roles = $info->getLocalRoles();
        } else {
            $local_roles = $info->translateRoleIds($local_roles);
        }
        return $global_roles + $local_roles;
    }
}
