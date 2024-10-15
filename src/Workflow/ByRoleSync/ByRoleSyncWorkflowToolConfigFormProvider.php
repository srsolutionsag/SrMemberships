<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\ByRoleSync;

use ILIAS\UI\Factory;
use srag\Plugins\SrMemberships\Config\Config;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Workflow\ByRoleSync\Config\ByRoleSyncConfig;
use srag\Plugins\SrMemberships\Workflow\ToolObjectConfig\ToolConfigFormProvider;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use ILIAS\UI\Component\Input\Field\Section;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ByRoleSyncWorkflowToolConfigFormProvider implements ToolConfigFormProvider
{
    public const ROLE_SELECTION = 'role_selection';
    /**
     * @var ByRoleSyncConfig
     * @readonly
     */
    private Config $config;
    /**
     * @readonly
     */
    private Factory $ui_factory;

    public function __construct(
        private Container $container,
        WorkflowContainer $workflow_container
    ) {
        $this->config = $workflow_container->getConfig();
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
