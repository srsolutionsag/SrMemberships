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

/**
 * @author      Fabian Schmid <fabian@sr.solutions>
 */
final class StandardSyncModes extends SyncModes
{
    public function __construct()
    {
        parent::__construct(
            SyncModes::syncMissing(),
            SyncModes::syncBidirectional(),
            SyncModes::syncRemove()
        );
    }
}
