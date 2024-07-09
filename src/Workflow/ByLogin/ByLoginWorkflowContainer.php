<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\ByLogin;

use ilSrMsAbstractGUI;
use ilSrMsByLoginConfigurationGUI;
use ilSrMsAbstractWorkflowProcessorGUI;
use ilSrMsStoreObjectConfigGUI;
use srag\Plugins\SrMemberships\Config\ConfigForm;
use srag\Plugins\SrMemberships\Config\General\GeneralConfig;
use srag\Plugins\SrMemberships\Config\Config;
use srag\Plugins\SrMemberships\Workflow\ByLogin\Config\Form;
use srag\Plugins\SrMemberships\Workflow\AbstractBaseWorkflowContainer;
use srag\Plugins\SrMemberships\Workflow\ToolObjectConfig\ToolConfigFormProvider;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Action\ActionHandler;
use srag\Plugins\SrMemberships\Workflow\ByLogin\Config\ByLoginConfig;
use srag\Plugins\SrMemberships\Workflow\ByLogin\Action\ByLoginActionHandler;
use ilSrMsBaseConfigurationGUI;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ByLoginWorkflowContainer extends AbstractBaseWorkflowContainer
{
    public function getWorkflowID(): string
    {
        return GeneralConfig::BY_LOGIN;
    }

    public function getConfig(): Config
    {
        return $this->container->config()->byLogin();
    }

    public function isToolAvailable(Context $context): bool
    {
        // depends on settings
        $offered_to = $this->getConfig()->get(ByLoginConfig::F_OFFER_WORKFLOW_TO) ?? [];
        if ($offered_to === [-1]) { // the tool will be shown if the user has manage members permission on the object
            return true;
        }

        return $this->container->userAccessInfoProvider()->isUserInAtLeastOneRole($context->getUserId(), $offered_to);
    }

    public function getConfigClass(): ilSrMsAbstractGUI
    {
        return new ilSrMsByLoginConfigurationGUI();
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
        return new ByLoginWorkflowToolConfigFormProvider(
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
        return new ByLoginActionHandler($this->container);
    }
}
