<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\ByLogin\Config;

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
                $this->translator->txt('object_types_info')
            ),
            $this->getAllOrMultiSelect(
                ByLoginConfig::F_OFFER_WORKFLOW_TO,
                $this->translator->txt(ByLoginConfig::F_OFFER_WORKFLOW_TO),
                $this->translator->txt('offer_workflow_to_object_admins'),
                -1,
                $this->getSelectableRoles(),
                $this->translator->txt('offer_workflow_to_info')
            ),
            $this->getSelect(
                ByLoginConfig::F_MATCHING_FIELD,
                $this->translator->txt(ByLoginConfig::F_MATCHING_FIELD),
                [
                    ByLoginConfig::MATCHING_FIELD_LOGIN => $this->translator->txt(ByLoginConfig::MATCHING_FIELD_LOGIN),
                    ByLoginConfig::MATCHING_FIELD_EXT_ACCOUNT => $this->translator->txt(
                        ByLoginConfig::MATCHING_FIELD_EXT_ACCOUNT
                    ),
                ],
                $this->translator->txt(ByLoginConfig::F_MATCHING_FIELD . '_byline')
            )->withRequired(true)
        ];
    }
}
