<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\Config;

use srag\Plugins\SrMemberships\Config\Config;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
interface WorkflowConfig extends Config
{
    public const F_OBJECT_TYPES = 'object_types';

    public function getActivatedForTypes(): array;
}
