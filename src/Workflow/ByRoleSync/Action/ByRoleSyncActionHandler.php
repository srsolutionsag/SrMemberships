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

namespace srag\Plugins\SrMemberships\Workflow\ByRoleSync\Action;

use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Workflow\Mode\ModesLegacy;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Action\BaseActionHandler;
use srag\Plugins\SrMemberships\Workflow\ByRoleSync\ByRoleSyncWorkflowToolConfigFormProvider;
use srag\Plugins\SrMemberships\Action\Summary;
use srag\Plugins\SrMemberships\Workflow\Mode\Sync\SyncModes;
use srag\Plugins\SrMemberships\Workflow\Mode\Run\RunModes;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ByRoleSyncActionHandler extends BaseActionHandler
{
    public function performActions(
        WorkflowContainer $workflow_container,
        Context $context,
        SyncModes $sync_modes,
        RunModes $run_modes
    ) : Summary {
        if ($summary = $this->checkRunMode($context, $run_modes)) {
            return $summary;
        }

        $object_config = $this->container->toolObjectConfigRepository()->get(
            $context->getCurrentRefId(),
            $workflow_container
        );

        $role_ids = $object_config[ByRoleSyncWorkflowToolConfigFormProvider::ROLE_SELECTION] ?? [];
        $person_list = $this->person_list_generators->byRoleIds($role_ids);
        $account_list = $this->persons_to_accounts->translate($person_list);

        return $this->generalHandling(
            $context,
            $account_list,
            $person_list,
            $sync_modes
        );
    }
}
