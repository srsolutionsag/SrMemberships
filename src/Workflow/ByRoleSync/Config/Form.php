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

use srag\Plugins\SrMemberships\Config\AbstractConfigForm;
use srag\Plugins\SrMemberships\Provider\Context\ObjectInfoProvider;
use srag\Plugins\SrMemberships\Workflow\Config\WorkflowConfig;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class Form extends AbstractConfigForm
{
    private function getGlobalRoles(): array
    {
        return $this->container->objectInfoProvider()->getGlobalRoles();
    }

    private function getLocalRoles(): array
    {
        return $this->container->objectInfoProvider()->getLocalRoles();
    }

    protected function getFields(): array
    {
        return [
            $this->getMultiSelect(
                WorkflowConfig::F_OBJECT_TYPES,
                $this->translator->txt(WorkflowConfig::F_OBJECT_TYPES),
                [
                    ObjectInfoProvider::TYPE_CRS => $this->translator->txt('object_type_crs'),
                    ObjectInfoProvider::TYPE_GRP => $this->translator->txt('object_type_grp'),
                ],
                $this->translator->txt('object_types_info')
            ),
            $this->getAllOrMultiSelect(
                ByRoleSyncConfig::F_OFFER_WORKFLOW_TO,
                $this->translator->txt(ByRoleSyncConfig::F_OFFER_WORKFLOW_TO),
                $this->translator->txt('offer_workflow_to_object_admins'),
                -1,
                $this->getSelectableRoles(),
                $this->translator->txt('offer_workflow_to_info')
            ),
            $this->getAllOrMultiSelect(
                ByRoleSyncConfig::F_SELECTABLE_GLOBAL_ROLES,
                $this->translator->txt(ByRoleSyncConfig::F_SELECTABLE_GLOBAL_ROLES),
                $this->translator->txt('selectable_global_roles_all'),
                -1,
                $this->getGlobalRoles(),
                $this->translator->txt('selectable_global_roles_info')
            ),
            $this->getAllOrMultiSelect(
                ByRoleSyncConfig::F_SELECTABLE_LOCAL_ROLES,
                $this->translator->txt(ByRoleSyncConfig::F_SELECTABLE_LOCAL_ROLES),
                $this->translator->txt('selectable_local_roles_all'),
                -1,
                $this->getLocalRoles(),
                $this->translator->txt('selectable_local_roles_info')
            )
        ];
    }
}
