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

namespace srag\Plugins\SrMemberships\Workflow\Mode\Sync;

use srag\Plugins\SrMemberships\Workflow\Mode\AbstractModes;
use srag\Plugins\SrMemberships\Workflow\Mode\Mode;

/**
 * @author      Fabian Schmid <fabian@sr.solutions>
 *
 * @description SyncModes decide how to handle the list of persons specified in an object.
 * SyncModes start with an ID >=32
 */
class SyncModes extends AbstractModes
{
    public const SYNC_MISSING_USERS = 32;
    public const SYNC_BIDIRECTIONAL = 64;
    public const SYNC_REMOVE = 128;

    protected function getPrefix() : string
    {
        return "sync_mode_";
    }

    public static function syncMissing() : Mode
    {
        return new Mode(
            self::SYNC_MISSING_USERS,
            self::getModeTitle(self::SYNC_MISSING_USERS),
            true
        );
    }

    public function getDefaultMode() : Mode
    {
        return self::syncMissing();
    }

    public static function syncBidirectional() : Mode
    {
        return new Mode(
            self::SYNC_BIDIRECTIONAL,
            self::getModeTitle(self::SYNC_BIDIRECTIONAL),
            true
        );
    }

    public static function syncRemove() : Mode
    {
        return new Mode(
            self::SYNC_REMOVE,
            self::getModeTitle(self::SYNC_REMOVE),
            true
        );
    }

    public static function generic(int $mode_id, bool $selectable) : Mode
    {
        switch ($mode_id) {
            case self::SYNC_MISSING_USERS:
                return self::syncMissing();
            case self::SYNC_BIDIRECTIONAL:
                return self::syncBidirectional();
            case self::SYNC_REMOVE:
                return self::syncRemove();
            default:
                return new Mode($mode_id, self::getModeTitle($mode_id), $selectable);
        }
    }
}
