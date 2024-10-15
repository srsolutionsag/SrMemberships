<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Config\General;

use srag\Plugins\SrMemberships\Config\AbstractConfigForm;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class GeneralConfigForm extends AbstractConfigForm
{
    protected function getFields(): array
    {
        $all_workflows = [];
        foreach ($this->container->workflows()->getAllWorkflows() as $workflow) {
            $workflow_id = $workflow->getWorkflowID();
            $all_workflows[$workflow_id] = $this->translator->txt(
                'workflow_' . $workflow_id
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
