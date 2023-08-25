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
use srag\Plugins\SrMemberships\Config\General\GeneralConfig;
use srag\Plugins\SrMemberships\Workflow\ByRoleSync\ByRoleSyncWorkflowContainer;
use srag\Plugins\SrMemberships\Workflow\ByLogin\ByLoginWorkflowContainer;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class WorkflowContainerRepository
{
    protected $all_workflow_containers = [];

    /**
     * @var array
     */
    protected $enabled_workflow_containers;
    /**
     * @var \srag\Plugins\SrMemberships\Container\Container
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->enabled_workflow_containers = [];

        // All available workflows
        $this->all_workflow_containers = [
            GeneralConfig::BY_ROLE_SYNC => new ByRoleSyncWorkflowContainer($this->container),
            GeneralConfig::BY_LOGIN => new ByLoginWorkflowContainer($this->container)
            // TODO add more workflows here, later we should use an artifact to load all workflows.
        ];

        // Init containers from config
        $enabled_workflow_ids = $this->container->config()->general()->getEnabledWorkflows();
        foreach ($enabled_workflow_ids as $workflow_id) {
            if (isset($this->all_workflow_containers[$workflow_id])) {
                $this->enabled_workflow_containers[$workflow_id] = $this->all_workflow_containers[$workflow_id];
            }
        }
    }

    /**
     * @return WorkflowContainer[]
     */
    public function getEnabledWorkflows() : array
    {
        return $this->enabled_workflow_containers;
    }

    public function getAllWorkflows() : array
    {
        return $this->all_workflow_containers;
    }

    /**
     * @throws \InvalidArgumentException if feature is not activated or exists.
     */
    public function getWorkflowById(string $workflow_id) : WorkflowContainer
    {
        if (!isset($this->all_workflow_containers[$workflow_id])) {
            throw new \InvalidArgumentException("Workflow with id $workflow_id does not exist.");
        }

        return $this->all_workflow_containers[$workflow_id];
    }

    public function getEnabledWorkflowById(string $workflow_id) : ?WorkflowContainer
    {
        return $this->enabled_workflow_containers[$workflow_id] ?? null;
    }
}
