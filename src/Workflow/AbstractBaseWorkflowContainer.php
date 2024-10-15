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

use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Provider\Tool\WorkflowToolProvider;
use srag\Plugins\SrMemberships\Provider\Tool\StandardWorkflowToolProvider;
use srag\Plugins\SrMemberships\Workflow\Mode\Sync\StandardSyncModes;
use srag\Plugins\SrMemberships\Workflow\Mode\Modes;
use srag\Plugins\SrMemberships\Workflow\Mode\Run\StandardRunModes;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
abstract class AbstractBaseWorkflowContainer implements WorkflowContainer
{
    public function __construct(protected Container $container)
    {
    }

    public function isActivated(): bool
    {
        return $this->container->config()->general()->getEnabledWorkflows()[$this->getWorkflowID()] ?? false;
    }

    public function getToolProvider(): WorkflowToolProvider
    {
        return new StandardWorkflowToolProvider(
            $this->container,
            $this
        );
    }

    public function getPossiblesSyncModes(): Modes
    {
        return new StandardSyncModes();
    }

    public function getPossiblesRunModes(): Modes
    {
        return new StandardRunModes();
    }
}
