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
use srag\Plugins\SrMemberships\Workflow\Mode\Sync\SyncModes;
use srag\Plugins\SrMemberships\Workflow\Mode\Run\RunModes;

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

    public function getSyncMode(int $ref_id, WorkflowContainer $workflow_container) : ?Mode
    {
        $q = "SELECT mode_id FROM " . self::TABLE_NAME . " WHERE context_ref_id = %s AND workflow_id = %s AND mode_id >= 32";
        $r = $this->db->queryF($q, ['integer', 'text'], [$ref_id, $workflow_container->getWorkflowID()]);
        if ($r->numRows() === 0) {
            return null;
        }
        $row = $this->db->fetchAssoc($r);
        return SyncModes::generic((int) $row['mode_id'], true);
    }

    public function storeSyncMode(int $ref_id, WorkflowContainer $workflow_container, Mode $mode) : void
    {
        // remove all other sync modes
        $this->db->manipulateF(
            "DELETE FROM " . self::TABLE_NAME . " WHERE context_ref_id = %s AND workflow_id = %s AND mode_id >= 32",
            ['integer', 'text'],
            [$ref_id, $workflow_container->getWorkflowID()]
        );
        // store new sync mode
        $this->db->insert(
            self::TABLE_NAME,
            [
                'context_ref_id' => ['integer', $ref_id],
                'workflow_id' => ['text', $workflow_container->getWorkflowID()],
                'mode_id' => ['integer', $mode->getModeId()],
            ]
        );
    }

    public function getRunModes(int $ref_id, WorkflowContainer $workflow_container) : ?Modes
    {
        $q = "SELECT mode_id FROM " . self::TABLE_NAME . " WHERE context_ref_id = %s AND workflow_id = %s AND mode_id < 32";
        $r = $this->db->queryF($q, ['integer', 'text'], [$ref_id, $workflow_container->getWorkflowID()]);
        if ($r->numRows() === 0) {
            return null;
        }
        $modes = new RunModes();
        while ($row = $this->db->fetchAssoc($r)) {
            $modes->addMode(RunModes::generic((int) $row['mode_id'], true));
        }
        return $modes;
    }

    public function storeRunModes(int $ref_id, WorkflowContainer $workflow_container, RunModes $modes) : void
    {
        // first delete all existing
        $this->db->manipulateF(
            "DELETE FROM " . self::TABLE_NAME . " WHERE context_ref_id = %s AND workflow_id = %s AND mode_id < 32",
            ['integer', 'text'],
            [$ref_id, $workflow_container->getWorkflowID()]
        );
        // then store new
        foreach ($modes->getModes() as $mode) {
            $this->db->insert(
                self::TABLE_NAME,
                [
                    'context_ref_id' => ['integer', $ref_id],
                    'workflow_id' => ['text', $workflow_container->getWorkflowID()],
                    'mode_id' => ['integer', $mode->getModeId()],
                ]
            );
        }
    }
}
