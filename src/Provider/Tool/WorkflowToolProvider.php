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
interface WorkflowToolProvider
{
    public function getTool(
        Context $context,
        ToolFactory $tool_factory,
        PluginIdentificationProvider $identification_factory
    ) : ?Tool;
}
