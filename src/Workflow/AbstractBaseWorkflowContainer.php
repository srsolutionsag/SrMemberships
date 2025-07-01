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
use srag\Plugins\SrMemberships\Config\General\GeneralConfig;
use srag\Plugins\SrMemberships\Translator;
use srag\Plugins\SrMemberships\Workflow\Mode\Mode;
use srag\Plugins\SrMemberships\Workflow\Mode\Run\RunModes;

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
        return in_array($this->getWorkflowID(), $this->container->config()->general()->getEnabledWorkflows(), true);
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

    public function getGeneralConfig(): GeneralConfig
    {
        return $this->container->config()->general();
    }

    public function getWorkflowInfos(Translator $t, RunModes $run_modes, Mode $sync_mode, array $config_data): array
    {
        return [
            $t->txt($this->getWorkflowID() . '_' . 'source') . ':' => $t->txt(
                $this->getWorkflowID() . '_' . $config_data['type'] . '_list'
            ),
            $t->txt('sync_modes') . ':' => $t->txt(strtolower($sync_mode->getModeTitle())),
            $t->txt('run_modes') . ':' => implode(', ', $run_modes->getModesAsStrings($t))
        ];
    }

}
