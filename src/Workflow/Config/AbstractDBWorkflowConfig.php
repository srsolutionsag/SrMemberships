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

use srag\Plugins\SrMemberships\Config\AbstractDBConfig;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
abstract class AbstractDBWorkflowConfig extends AbstractDBConfig implements WorkflowConfig
{
    public function getActivatedForTypes(): array
    {
        return $this->get(self::F_OBJECT_TYPES, []);
    }
}
