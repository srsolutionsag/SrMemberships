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

namespace srag\Plugins\SrMemberships\Workflow\ByLogin\Config;

use srag\Plugins\SrMemberships\Config\AbstractConfigForm;
use srag\Plugins\SrMemberships\Provider\Context\ObjectInfoProvider;
use srag\Plugins\SrMemberships\Workflow\Config\WorkflowConfig;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class Form extends AbstractConfigForm
{
    protected function getFields() : array
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
