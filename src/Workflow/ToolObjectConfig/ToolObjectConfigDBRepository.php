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

use ilDBInterface;
use Generator;
use srag\Plugins\SrMemberships\Config\Packer;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Config\PackedValue;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ToolObjectConfigDBRepository implements ToolObjectConfigRepository
{
    use Packer;
    protected \ilDBInterface $db;

    public const TABLE_NAME = 'srms_object_config';

    public function __construct(\ilDBInterface $db)
    {
        $this->db = $db;
    }

    public function store(
        int $ref_id,
        WorkflowContainer $workflow_container,
        array $data
    ): void {
        $this->clear($ref_id, $workflow_container);
        $packed = $this->pack($data);
        $this->db->manipulateF(
            "INSERT INTO " . self::TABLE_NAME . " (context_ref_id, workflow_id, config_data) VALUES (%s, %s, %s)",
            ['integer', 'text', 'text'],
            [$ref_id, $workflow_container->getWorkflowId(), $packed->getPackedValue()]
        );
    }

    public function get(
        int $ref_id,
        WorkflowContainer $workflow_container
    ): ?array {
        $q = "SELECT * FROM " . self::TABLE_NAME . " WHERE context_ref_id = %s AND workflow_id = %s";
        $res = $this->db->queryF($q, ['integer', 'text'], [$ref_id, $workflow_container->getWorkflowId()]);
        $data = $this->db->fetchObject($res);
        return $this->unpack(new PackedValue($data->config_data ?? null, PackedValue::TYPE_ARRAY)) ?? null;
    }

    public function clear(
        int $ref_id,
        WorkflowContainer $workflow_container
    ): void {
        $this->db->manipulateF(
            "DELETE FROM " . self::TABLE_NAME . " WHERE context_ref_id = %s AND workflow_id = %s",
            ['integer', 'text'],
            [$ref_id, $workflow_container->getWorkflowId()]
        );
    }

    public function getAssignedRefIds(WorkflowContainer $workflow): Generator
    {
        $q = "SELECT DISTINCT context_ref_id FROM " . self::TABLE_NAME . " WHERE workflow_id = %s";
        $res = $this->db->queryF($q, ['text'], [$workflow->getWorkflowId()]);
        while ($row = $this->db->fetchAssoc($res)) {
            yield (int) $row['context_ref_id'];
        }
    }

    public function countAssignedWorkflows(int $ref_id): int
    {
        $q = "SELECT COUNT(*) AS cnt FROM " . self::TABLE_NAME . " WHERE context_ref_id = %s";
        $res = $this->db->queryF($q, ['integer'], [$ref_id]);
        $row = $this->db->fetchAssoc($res);
        return (int) $row['cnt'];
    }
}
