<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\Mode\Run;

use srag\Plugins\SrMemberships\Config\General\GeneralConfig;
use srag\Plugins\SrMemberships\Workflow\Mode\Mode;

/**
 * @author      Fabian Schmid <fabian@sr.solutions>
 */
final class PreselectedRunModes extends RunModes
{
    public function __construct(
        GeneralConfig $config,
    ) {
        // read from config
        $modes = array_map(
            static fn(int $mode_id): Mode => RunModes::generic($mode_id, true),
            array_map('intval', $config->getPreselectedRunModes())
        );

        parent::__construct(
            ...$modes,
        );
    }
}
