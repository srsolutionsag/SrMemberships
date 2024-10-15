<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
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
    public const F_USER_CREATION = 'user_creation';

    public function getActivatedForTypes(): array;
}
