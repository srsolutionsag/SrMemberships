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
use srag\Plugins\SrMemberships\Workflow\Mode\Modes;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Action\ActionHandler;
use srag\Plugins\SrMemberships\Action\BaseActionHandler;
use srag\Plugins\SrMemberships\Workflow\ByRoleSync\ByRoleSyncWorkflowToolConfigFormProvider;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ByRoleSyncActionHandler extends BaseActionHandler implements ActionHandler
{
    public function performActions(
        WorkflowContainer $workflow_container,
        Context $context,
        Modes $modes
    ): void {
        if ($context->isCli() && !$modes->isModeSet(Modes::RUN_AS_CRONJOB)) {
            return;
        }
        if (!$context->isCli() && !$modes->isModeSet(Modes::RUN_ON_SAVE)) {
            return;
        }

        $object_config = $this->container->toolObjectConfigRepository()->get(
            $context->getCurrentRefId(),
            $workflow_container
        );

        $role_ids = $object_config[ByRoleSyncWorkflowToolConfigFormProvider::ROLE_SELECTION] ?? [];
        $person_list = $this->person_list_generators->byRoleIds($role_ids);
        $account_list = $this->persons_to_accounts->translate($person_list);

        $current_members = $this->account_list_generators->fromContainerId($context->getCurrentRefId());
        $accounts_to_add = $this->account_list_generators->diff($account_list, $current_members);

        // Subscribe new members
        $this->action_builder->subscribe($context->getCurrentRefId())
                             ->performFor($accounts_to_add);

        if ($modes->isModeSet(Modes::REMOVE_DIFF)) {
            $accounts_ro_remove = $this->account_list_generators->diff($current_members, $account_list);

            // Unsubscribe members that are not in the role anymore
            $this->action_builder->unsubscribe($context->getCurrentRefId())
                                 ->performFor($accounts_ro_remove);
        }
    }

}
