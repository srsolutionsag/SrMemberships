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

namespace srag\Plugins\SrMemberships\Action;

use srag\Plugins\SrMemberships\Person\PersonsToAccounts;
use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Workflow\Mode\ModesLegacy;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Workflow\Mode\Sync\SyncModes;
use srag\Plugins\SrMemberships\Person\Account\AccountList;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Person\Persons\PersonList;
use srag\Plugins\SrMemberships\Workflow\Mode\Run\RunModes;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
abstract class BaseActionHandler implements ActionHandler
{
    /**
     * @var \srag\Plugins\SrMemberships\Person\Account\AccountListGenerators
     */
    protected $account_list_generators;
    /**
     * @var ActionBuilder
     */
    protected $action_builder;
    /**
     * @var \srag\Plugins\SrMemberships\Person\Persons\PersonListGenerators
     */
    protected $person_list_generators;
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var PersonsToAccounts
     */
    protected $persons_to_accounts;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->persons_to_accounts = new PersonsToAccounts($container->dic()->database());
        $this->person_list_generators = $container->personListGenerators();
        $this->account_list_generators = $container->accountListGenerators();
        $this->action_builder = new ActionBuilder($container);
    }

    protected function generalHandling(
        Context $context,
        AccountList $account_list,
        PersonList $not_found_persons,
        SyncModes $sync_modes
    ) : Summary {
        $current_members = $this->account_list_generators->fromContainerId($context->getCurrentRefId());

        // get first sync mode since currently only one is supported
        $sync_modes_array = $sync_modes->getModes();
        $sync_mode = reset($sync_modes_array);

        // run depending on sync mode
        switch ($sync_mode->getModeId()) {
            case SyncModes::SYNC_MISSING_USERS:
                // Subscribe members that are not already subscribed
                $missing_account_list = $this->account_list_generators->diff($account_list, $current_members);
                $this->action_builder->subscribe($context->getCurrentRefId())
                                     ->performFor($missing_account_list);

                return Summary::from($missing_account_list);
            case SyncModes::SYNC_BIDIRECTIONAL:
                // Subscribe members that are not already subscribed
                $missing_account_list = $this->account_list_generators->diff($account_list, $current_members);
                $this->action_builder->subscribe($context->getCurrentRefId())
                                     ->performFor($missing_account_list);
                $superfluous_account_list = $this->account_list_generators->diff($current_members, $account_list);
                // Unsubscribe members that are not the given list
                $this->action_builder->unsubscribe($context->getCurrentRefId())
                                     ->performFor($superfluous_account_list);

                return Summary::from($missing_account_list, $superfluous_account_list, $not_found_persons);

            case SyncModes::SYNC_REMOVE:
                // remove all from the given list
                $accounts_to_remove = $this->account_list_generators->intersect($account_list, $current_members);

                $this->action_builder->unsubscribe($context->getCurrentRefId())
                                     ->performFor($accounts_to_remove);

                return Summary::from(new AccountList(), $accounts_to_remove, $not_found_persons);
            default:
                break;
        }

        return Summary::empty();
    }

    public function getDeleteWorkflowURL(WorkflowContainer $workflow_container) : string
    {
        return $this->container->dic()->ctrl()->getLinkTargetByClass(
            [\ilUIPluginRouterGUI::class, get_class($workflow_container->getWorkflowToolFormProcessor())],
            \ilSrMsAbstractWorkflowProcessorGUI::CMD_HANDLE_WORKFLOW_DELETION
        );
    }

    protected function checkRunMode(Context $context, RunModes $run_modes) : ?Summary
    {
        if ($context->isCli() && !$run_modes->isRunAsCron()) {
            return Summary::null();
        }
        if (!$context->isCli() && !$run_modes->isRunOnSave()) {
            return Summary::null();
        }
        return null;
    }
}
