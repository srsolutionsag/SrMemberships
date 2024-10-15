<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\ToolObjectConfig;

use Generator;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
interface ToolObjectConfigRepository
{
    public function store(
        int $ref_id,
        WorkflowContainer $workflow_container,
        array $data
    ): void;

    public function get(
        int $ref_id,
        WorkflowContainer $workflow_container
    ): ?array;

    public function clear(
        int $ref_id,
        WorkflowContainer $workflow_container
    ): void;

    public function getAssignedRefIds(WorkflowContainer $workflow): Generator;

    public function countAssignedWorkflows(int $ref_id): int;
}
