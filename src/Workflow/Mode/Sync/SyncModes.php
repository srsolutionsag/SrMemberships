<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

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

    protected function getPrefix(): string
    {
        return "sync_mode_";
    }

    public static function syncMissing(): Mode
    {
        return new Mode(
            self::SYNC_MISSING_USERS,
            self::getModeTitle(self::SYNC_MISSING_USERS),
            true
        );
    }

    public function getDefaultMode(): Mode
    {
        return self::syncMissing();
    }

    public static function syncBidirectional(): Mode
    {
        return new Mode(
            self::SYNC_BIDIRECTIONAL,
            self::getModeTitle(self::SYNC_BIDIRECTIONAL),
            true
        );
    }

    public static function syncRemove(): Mode
    {
        return new Mode(
            self::SYNC_REMOVE,
            self::getModeTitle(self::SYNC_REMOVE),
            true
        );
    }

    public static function generic(int $mode_id, bool $selectable): Mode
    {
        return match ($mode_id) {
            self::SYNC_MISSING_USERS => self::syncMissing(),
            self::SYNC_BIDIRECTIONAL => self::syncBidirectional(),
            self::SYNC_REMOVE => self::syncRemove(),
            default => new Mode($mode_id, self::getModeTitle($mode_id), $selectable),
        };
    }
}
