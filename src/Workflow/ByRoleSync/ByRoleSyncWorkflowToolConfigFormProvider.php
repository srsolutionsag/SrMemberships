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

namespace srag\Plugins\SrMemberships\Workflow\ByRoleSync;

use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Container;
use srag\Plugins\SrMemberships\Workflow\ByRoleSync\Config\ByRoleSyncConfig;
use srag\Plugins\SrMemberships\Workflow\ToolObjectConfig\ToolConfigFormProvider;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use ILIAS\UI\Component\Input\Field\Section;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ByRoleSyncWorkflowToolConfigFormProvider implements ToolConfigFormProvider
{
    const ROLE_SELECTION = 'role_selection';
    /**
     * @var WorkflowContainer
     */
    private $workflow_container;
    /**
     * @var Container
     */
    private $container;
    /**
     * @var ByRoleSyncConfig
     */
    private $config;
    /**
     * @var \ILIAS\UI\Factory
     */
    private $ui_factory;

    public function __construct(
        Container $container,
        WorkflowContainer $workflow_container
    ) {
        $this->container = $container;
        $this->workflow_container = $workflow_container;
        $this->config = $this->workflow_container->getConfig();
        $this->ui_factory = $this->container->dic()->ui()->factory();
    }

    public function getFormSection(
        Context $context
    ): Section {
        $selectable_roles = $this->config->getAvailableRolesForSelection(
            $this->container->objectInfoProvider()
        );

        $role_selection = $this->ui_factory->input()->field()->multiSelect(
            $this->container->translator()->txt('by_role_sync_role_selection'),
            $selectable_roles,
            $this->container->translator()->txt('by_role_sync_role_selection_info')
        );

        return $this->ui_factory->input()->field()->section(
            [self::ROLE_SELECTION => $role_selection],
            $this->container->translator()->txt('by_role_sync_role_selection_header')
        );
    }
}
