<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\ByRoleSync;

use ilSrMsAbstractGUI;
use ilSrMsByRoleSyncConfigurationGUI;
use ilSrMsAbstractWorkflowProcessorGUI;
use ilSrMsStoreObjectConfigGUI;
use srag\Plugins\SrMemberships\Config\ConfigForm;
use srag\Plugins\SrMemberships\Config\General\GeneralConfig;
use srag\Plugins\SrMemberships\Config\Config;
use srag\Plugins\SrMemberships\Workflow\ByRoleSync\Config\Form;
use srag\Plugins\SrMemberships\Workflow\AbstractBaseWorkflowContainer;
use srag\Plugins\SrMemberships\Workflow\ToolObjectConfig\ToolConfigFormProvider;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Action\ActionHandler;
use srag\Plugins\SrMemberships\Workflow\ByRoleSync\Action\ByRoleSyncActionHandler;
use srag\Plugins\SrMemberships\Workflow\ByRoleSync\Config\ByRoleSyncConfig;
use ilSrMsBaseConfigurationGUI;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ByRoleSyncWorkflowContainer extends AbstractBaseWorkflowContainer
{
    public function getWorkflowID(): string
    {
        return GeneralConfig::BY_ROLE_SYNC;
    }

    public function getConfig(): Config
    {
        return $this->container->config()->byRoleSync();
    }

    public function isToolAvailable(Context $context): bool
    {
        // depends on settings
        $offered_to = $this->getConfig()->get(ByRoleSyncConfig::F_OFFER_WORKFLOW_TO) ?? [];
        if ($offered_to === [-1]) { // the tool will be shown if the user has manage members permission on the object
            return true;
        }

        return $this->container->userAccessInfoProvider()->isUserInAtLeastOneRole($context->getUserId(), $offered_to);
    }

    public function getConfigClass(): ilSrMsAbstractGUI
    {
        return new ilSrMsByRoleSyncConfigurationGUI();
    }

    public function getConfigForm(): ConfigForm
    {
        return new Form(
            $this->getConfigClass(),
            ilSrMsBaseConfigurationGUI::CMD_SAVE,
            $this->getConfig(),
            $this->container
        );
    }

    public function getWorkflowToolForm(): ToolConfigFormProvider
    {
        return new ByRoleSyncWorkflowToolConfigFormProvider(
            $this->container,
            $this
        );
    }

    public function getWorkflowToolFormProcessor(): ilSrMsAbstractWorkflowProcessorGUI
    {
        return new ilSrMsStoreObjectConfigGUI();
    }

    public function getActionHandler(Context $context): ActionHandler
    {
        return new ByRoleSyncActionHandler($this->container);
    }
}
