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

namespace srag\Plugins\SrMemberships\Workflow\ByRoleSync;

use srag\Plugins\SrMemberships\Config\ConfigForm;
use srag\Plugins\SrMemberships\Config\General\GeneralConfig;
use srag\Plugins\SrMemberships\Config\Config;
use srag\Plugins\SrMemberships\Workflow\ByRoleSync\Config\Form;
use srag\Plugins\SrMemberships\Workflow\AbstractBaseWorkflowContainer;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Workflow\ToolObjectConfig\ToolConfigFormProvider;
use srag\Plugins\SrMemberships\Workflow\Mode\Modes;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Action\ActionHandler;
use srag\Plugins\SrMemberships\Workflow\ByRoleSync\Action\ByRoleSyncActionHandler;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ByRoleSyncWorkflowContainer extends AbstractBaseWorkflowContainer implements WorkflowContainer
{
    public function getWorkflowID(): string
    {
        return GeneralConfig::BY_ROLE_SYNC;
    }

    public function getPossiblesModes(): Modes
    {
        return new Modes(
            Modes::cron(),
            Modes::adHoc(),
            Modes::removeDiff(),
            Modes::runAsCronJob(),
            Modes::runOnSave()
        );
    }

    public function getConfig(): Config
    {
        return $this->container->config()->byRoleSync();
    }

    public function getConfigClass(): \ilSrMsAbstractGUI
    {
        return new \ilSrMsByRoleSyncConfigurationGUI();
    }

    public function getConfigForm(): ConfigForm
    {
        return new Form(
            $this->getConfigClass(),
            \ilSrMsByRoleSyncConfigurationGUI::CMD_SAVE,
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

    public function getWorkflowToolFormProcessor(): \ilSrMsAbstractWorkflowProcessorGUI
    {
        return new \ilSrMsStoreObjectConfigGUI();
    }

    public function getActionHandler(Context $context): ActionHandler
    {
        return new ByRoleSyncActionHandler($this->container);
    }

}
