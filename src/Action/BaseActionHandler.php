<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Action;

use srag\Plugins\SrMemberships\Person\Account\AccountListGenerators;
use srag\Plugins\SrMemberships\Person\Persons\PersonListGenerators;
use ilUIPluginRouterGUI;
use ilSrMsAbstractWorkflowProcessorGUI;
use srag\Plugins\SrMemberships\Person\PersonsToAccounts;
use srag\Plugins\SrMemberships\Container\Container;
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
    protected AccountListGenerators $account_list_generators;
    protected ActionBuilder $action_builder;
    protected PersonListGenerators $person_list_generators;

    protected PersonsToAccounts $persons_to_accounts;

    public function __construct(protected Container $container)
    {
        $this->persons_to_accounts = new PersonsToAccounts($this->container->dic()->database());
        $this->person_list_generators = $this->container->personListGenerators();
        $this->account_list_generators = $this->container->accountListGenerators();
        $this->action_builder = new ActionBuilder($this->container);
    }

    public function newUser(array $data): \ilObjUser
    {
        return new \ilObjUser();
    }

    public function getNotFoundPersonsList(WorkflowContainer $workflow_container, Context $context): PersonList
    {
        return new PersonList(); //???
    }


    public function getRawData(
        WorkflowContainer $workflow_container,
        Context $context
    ): array {
        return [];
    }

    protected function generalHandling(
        Context $context,
        AccountList $account_list,
        PersonList $not_found_persons,
        SyncModes $sync_modes
    ): Summary {
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

                return Summary::from($missing_account_list, null, $not_found_persons);
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

    public function getDeleteWorkflowURL(WorkflowContainer $workflow_container): string
    {
        return $this->container->dic()->ctrl()->getLinkTargetByClass(
            [ilUIPluginRouterGUI::class, $workflow_container->getWorkflowToolFormProcessor()::class],
            ilSrMsAbstractWorkflowProcessorGUI::CMD_HANDLE_WORKFLOW_DELETION
        );
    }

    protected function checkRunMode(Context $context, RunModes $run_modes): ?Summary
    {
        if ($context->isCli() && !$run_modes->isRunAsCron()) {
            return Summary::null();
        }
        if ($context->isCli()) {
            return null;
        }
        if ($run_modes->isRunOnSave()) {
            return null;
        }
        return Summary::null();
    }
}
