<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow;

use ilSrMsAbstractGUI;
use ilSrMsAbstractWorkflowProcessorGUI;
use srag\Plugins\SrMemberships\Config\ConfigForm;
use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Config\Config;
use srag\Plugins\SrMemberships\Provider\Tool\WorkflowToolProvider;
use srag\Plugins\SrMemberships\Workflow\ToolObjectConfig\ToolConfigFormProvider;
use srag\Plugins\SrMemberships\Action\ActionHandler;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Workflow\Mode\Modes;
use srag\Plugins\SrMemberships\Workflow\Mode\Run\RunModes;
use srag\Plugins\SrMemberships\Workflow\Mode\Sync\SyncModes;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
interface WorkflowContainer
{
    public function __construct(Container $container);

    /**
     * @return SyncModes
     */
    public function getPossiblesSyncModes(): Modes;

    /**
     * @return RunModes
     */
    public function getPossiblesRunModes(): Modes;

    public function getWorkflowID(): string;

    public function getConfigClass(): ilSrMsAbstractGUI;

    public function isActivated(): bool;

    public function isToolAvailable(Context $context): bool;

    public function getConfig(): Config;

    public function getConfigForm(): ConfigForm;

    public function getToolProvider(): WorkflowToolProvider;

    public function getWorkflowToolForm(): ToolConfigFormProvider;

    public function getWorkflowToolFormProcessor(): ilSrMsAbstractWorkflowProcessorGUI;

    public function getActionHandler(Context $context): ActionHandler;
}
