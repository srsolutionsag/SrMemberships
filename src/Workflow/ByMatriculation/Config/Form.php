<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\ByMatriculation\Config;

use srag\Plugins\SrMemberships\Config\AbstractConfigForm;
use srag\Plugins\SrMemberships\Provider\Context\ObjectInfoProvider;
use srag\Plugins\SrMemberships\Workflow\Config\WorkflowConfig;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class Form extends AbstractConfigForm
{
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
                $this->translator->txt('object_types_info'),
            ),

            $this->getAllOrMultiSelect(
                WorkflowConfig::F_USER_CREATION,
                $this->translator->txt(WorkflowConfig::F_USER_CREATION),
                $this->translator->txt(WorkflowConfig::F_USER_CREATION . '_deactivated'),
                -1,
                $this->container->objectInfoProvider()->getGlobalRoles(),
                $this->translator->txt(WorkflowConfig::F_USER_CREATION . '_role_info'),
                $this->translator->txt(WorkflowConfig::F_USER_CREATION . '_info'),
            ),

            $this->getAllOrMultiSelect(
                ByMatriculationConfig::F_OFFER_WORKFLOW_TO,
                $this->translator->txt(ByMatriculationConfig::F_OFFER_WORKFLOW_TO),
                $this->translator->txt('offer_workflow_to_object_admins'),
                -1,
                $this->getSelectableRoles(),
                $this->translator->txt('offer_workflow_to_info')
            )
        ];
    }
}
