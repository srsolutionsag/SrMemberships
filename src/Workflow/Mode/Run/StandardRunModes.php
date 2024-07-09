<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\Mode\Run;

/**
 * @author      Fabian Schmid <fabian@sr.solutions>
 */
final class StandardRunModes extends RunModes
{
    public function __construct()
    {
        parent::__construct(
            RunModes::runOnSave(),
            RunModes::runAsCronJob()
        );
    }
}
