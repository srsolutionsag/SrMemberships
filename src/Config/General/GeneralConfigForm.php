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

namespace srag\Plugins\SrMemberships\Config\General;

use srag\Plugins\SrMemberships\Config\AbstractConfigForm;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class GeneralConfigForm extends AbstractConfigForm
{
    protected function getFields(): array
    {
        $all_workflows = [];
        foreach ($this->container->workflows()->getAllWorkflows() as $workflow) {
            $all_workflows[$workflow->getWorkflowID()] = $this->translator->txt(
                'workflow_' . $workflow->getWorkflowID()
            );
        }

        return [
            $this->getMultiSelect(
                GeneralConfig::F_ENABLED_WORKFLOWS,
                $this->translator->txt('enabled_workflows'),
                $all_workflows,
                $this->translator->txt('enabled_workflows_info')
            ),
        ];
    }
}
