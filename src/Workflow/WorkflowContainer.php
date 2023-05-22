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

namespace srag\Plugins\SrMemberships\Workflow;

use srag\Plugins\SrMemberships\Config\ConfigForm;
use srag\Plugins\SrMemberships\Container;
use srag\Plugins\SrMemberships\Config\Config;
use srag\Plugins\SrMemberships\Provider\Tool\WorkflowToolProvider;
use srag\Plugins\SrMemberships\Workflow\ToolObjectConfig\ToolConfigFormProvider;
use srag\Plugins\SrMemberships\Workflow\Mode\Modes;
use srag\Plugins\SrMemberships\Action\ActionHandler;
use srag\Plugins\SrMemberships\Provider\Context\Context;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
interface WorkflowContainer
{
    public function __construct(Container $container);

    public function getPossiblesModes(): Modes;

    public function getWorkflowID(): string;

    public function getConfigClass(): \ilSrMsAbstractGUI;

    public function isActivated(): bool;
    public function isToolAvailable(Context $context): bool;

    public function getConfig(): Config;

    public function getConfigForm(): ConfigForm;

    public function getToolProvider(): WorkflowToolProvider;

    public function getWorkflowToolForm(): ToolConfigFormProvider;

    public function getWorkflowToolFormProcessor(): \ilSrMsAbstractWorkflowProcessorGUI;

    public function getActionHandler(Context $context): ActionHandler;
}
