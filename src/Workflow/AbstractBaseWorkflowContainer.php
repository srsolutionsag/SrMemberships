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

use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Provider\Tool\WorkflowToolProvider;
use srag\Plugins\SrMemberships\Provider\Tool\StandardWorkflowToolProvider;
use srag\Plugins\SrMemberships\Workflow\Mode\ModesLegacy;
use srag\Plugins\SrMemberships\Workflow\Mode\Sync\StandardSyncModes;
use srag\Plugins\SrMemberships\Workflow\Mode\Modes;
use srag\Plugins\SrMemberships\Workflow\Mode\Run\StandardRunModes;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
abstract class AbstractBaseWorkflowContainer implements WorkflowContainer
{
    /**
     * @var \srag\Plugins\SrMemberships\Container\Container
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function isActivated() : bool
    {
        return $this->container->config()->general()->getEnabledWorkflows()[$this->getWorkflowID()] ?? false;
    }

    public function getToolProvider() : WorkflowToolProvider
    {
        return new StandardWorkflowToolProvider(
            $this->container,
            $this
        );
    }

    public function getPossiblesSyncModes() : Modes
    {
        return new StandardSyncModes();
    }

    public function getPossiblesRunModes() : Modes
    {
        return new StandardRunModes();
    }
}
