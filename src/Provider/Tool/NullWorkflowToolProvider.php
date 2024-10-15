<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Provider\Tool;

use ILIAS\GlobalScreen\Scope\Tool\Factory\Tool;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use ILIAS\GlobalScreen\Scope\Tool\Factory\ToolFactory;
use ILIAS\GlobalScreen\Identification\PluginIdentificationProvider;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class NullWorkflowToolProvider implements WorkflowToolProvider
{
    public function getTool(
        Context $context,
        ToolFactory $tool_factory,
        PluginIdentificationProvider $identification_factory
    ): ?Tool {
        return null;
    }
}
