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

use InvalidArgumentException;
use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Config\General\GeneralConfig;
use srag\Plugins\SrMemberships\Workflow\ByRoleSync\ByRoleSyncWorkflowContainer;
use srag\Plugins\SrMemberships\Workflow\ByLogin\ByLoginWorkflowContainer;
use srag\Plugins\SrMemberships\Workflow\ByMatriculation\ByMatriculationWorkflowContainer;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class WorkflowContainerRepository
{
    protected $all_workflow_containers = [];

    /**
     * @var array
     */
    protected $enabled_workflow_containers = [];

    public function __construct(protected Container $container)
    {
        // All available workflows
        $this->all_workflow_containers = [
            GeneralConfig::BY_ROLE_SYNC => new ByRoleSyncWorkflowContainer($this->container),
            GeneralConfig::BY_LOGIN => new ByLoginWorkflowContainer($this->container),
            GeneralConfig::BY_MATRICULATION => new ByMatriculationWorkflowContainer($this->container),
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
    public function getEnabledWorkflows(): array
    {
        return $this->enabled_workflow_containers;
    }

    public function getAllWorkflows(): array
    {
        return $this->all_workflow_containers;
    }

    /**
     * @throws InvalidArgumentException if feature is not activated or exists.
     */
    public function getWorkflowById(string $workflow_id): WorkflowContainer
    {
        if (!isset($this->all_workflow_containers[$workflow_id])) {
            throw new InvalidArgumentException("Workflow with id $workflow_id does not exist.");
        }

        return $this->all_workflow_containers[$workflow_id];
    }

    public function getEnabledWorkflowById(string $workflow_id): ?WorkflowContainer
    {
        return $this->enabled_workflow_containers[$workflow_id] ?? null;
    }
}
