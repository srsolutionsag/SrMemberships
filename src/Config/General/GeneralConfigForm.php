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
use srag\Plugins\SrMemberships\Workflow\Mode\Run\RunModes;

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

            $this->getMultiSelect(
                GeneralConfig::F_PRESELECTED_RUN_MODES,
                $this->translator->txt('preselected_run_modes'),
                [
                    RunModes::RUN_ON_SAVE => $this->translator->txt('run_on_save'),
                    RunModes::RUN_AS_CRONJOB => $this->translator->txt('run_as_cronjob'),
                ],
                $this->translator->txt('preselected_run_modes_info')
            ),
            $this->getCheckbox(
                GeneralConfig::F_SHOW_INFO_TOOL,
                $this->translator->txt('show_info_tool'),
                $this->translator->txt('show_info_tool_info')
            ),
        ];
    }
}
