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
 */

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\Mode;

use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ObjectModeDBRepository implements ObjectModeRepository
{
    private const TABLE_NAME = 'srms_object_mode';
    /**
     * @var \ilDBInterface
     */
    private $db;
    /**
     * @var array
     */
    protected $cache = [];

    public function __construct(\ilDBInterface $db)
    {
        $this->db = $db;
    }

    private function hasAny(int $ref_id, string $workflow_id) : bool
    {
        $q = "SELECT * FROM " . self::TABLE_NAME . " WHERE context_ref_id = %s AND workflow_id = %s ";
        $r = $this->db->queryF($q, ['integer', 'text'], [$ref_id, $workflow_id]);
        return $r->numRows() > 0;
    }

    private function has(int $ref_id, string $workflow_id, int $mode_id) : bool
    {
        $q = "SELECT * FROM " . self::TABLE_NAME . " WHERE context_ref_id = %s AND workflow_id = %s AND mode_id = %s";
        $r = $this->db->queryF($q, ['integer', 'text', 'integer'], [$ref_id, $workflow_id, $mode_id]);
        return $r->numRows() > 0;
    }

    public function clear(int $ref_id, WorkflowContainer $workflow_container) : void
    {
        $this->db->manipulateF(
            "DELETE FROM " . self::TABLE_NAME . " WHERE context_ref_id = %s AND workflow_id = %s",
            ['integer', 'text'],
            [$ref_id, $workflow_container->getWorkflowId()]
        );
    }

    public function store(
        int $ref_id,
        WorkflowContainer $workflow_container,
        Modes $modes
    ) : void {
        $this->clear($ref_id, $workflow_container);
        foreach ($modes->getModes() as $mode) {
            $this->db->manipulateF(
                "INSERT INTO " . self::TABLE_NAME . " (context_ref_id, workflow_id, mode_id) VALUES (%s, %s, %s)",
                ['integer', 'text', 'integer'],
                [$ref_id, $workflow_container->getWorkflowId(), $mode->getModeId()]
            );
        }
    }

    public function storeFromArrayOfModeIds(
        int $ref_id,
        WorkflowContainer $workflow_container,
        array $mode_ids
    ) : void {
        $this->store(
            $ref_id,
            $workflow_container,
            new Modes(
                ...array_map(function (int $mode_id) {
                    return Modes::generic($mode_id, true);
                }, $mode_ids)
            )
        );
    }

    public function get(
        int $ref_id,
        WorkflowContainer $workflow_container
    ) : ?Modes {
        if (!$this->hasAny($ref_id, $workflow_container->getWorkflowId())) {
            return new Modes();
        }

        $r = $this->db->queryF(
            "SELECT * FROM " . self::TABLE_NAME . " WHERE context_ref_id = %s AND workflow_id = %s",
            ['integer', 'text'],
            [$ref_id, $workflow_container->getWorkflowId()]
        );
        $modes = [];
        while ($item = $this->db->fetchObject($r)) {
            $modes[] = Modes::generic((int) $item->mode_id, true);
        }

        return new Modes(...$modes);
    }
}
