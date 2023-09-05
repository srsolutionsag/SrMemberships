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
use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Workflow\Config\WorkflowConfig;
use ILIAS\GlobalScreen\Scope\Tool\Factory\ToolFactory;
use ILIAS\GlobalScreen\Identification\PluginIdentificationProvider;
use srag\Plugins\SrMemberships\Workflow\WorkflowFormBuilder;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class StandardWorkflowToolProvider implements WorkflowToolProvider
{
    /**
     * @var WorkflowConfig
     */
    private $workflow_config;
    /**
     * @var \ILIAS\UI\Factory
     */
    private $ui_factory;
    /**
     * @var \ILIAS\UI\Renderer
     */
    private $ui_renderer;
    /**
     * @var WorkflowFormBuilder
     */
    private $form_builder;
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var WorkflowContainer
     */
    protected $workflow_container;

    public function __construct(
        Container $container,
        WorkflowContainer $workflow_container
    ) {
        $this->container = $container;
        $this->workflow_container = $workflow_container;
        $this->workflow_config = $workflow_container->getConfig();
        //
        $this->ui_factory = $container->dic()->ui()->factory();
        $this->ui_renderer = $container->dic()->ui()->renderer();

        $this->form_builder = new WorkflowFormBuilder(
            $container
        );
    }

    public function getTool(
        Context $context,
        ToolFactory $tool_factory,
        PluginIdentificationProvider $identification_factory
    ) : ?Tool {
        if (!$this->hasTool($context)) {
            return null;
        }
        $workflow_id = $this->workflow_container->getWorkflowID();
        $form = $this->form_builder->getForm($context, $this->workflow_container);

        $title = $this->container->translator()->txt('workflow_' . $workflow_id);
        $identification = $identification_factory->identifier($workflow_id);

        $components = [];

        $remove_button = $this->ui_factory->button()->shy(
            $this->container->translator()->txt('remove_workflow'),
            $this->workflow_container->getActionHandler($context)->getDeleteWorkflowURL(
                $this->workflow_container
            )
        );

        if ($this->container->toolObjectConfigRepository()->get(
                $context->getCurrentRefId(), $this->workflow_container
            ) !== null) {
            $components[] = $this->ui_factory->panel()->secondary()->legacy(
                $this->container->translator()->txt('actions'),
                $this->ui_factory->legacy('')
            )->withActions($this->ui_factory->dropdown()->standard([
                $remove_button
            ]));
        }


        if ($this->container->toolObjectConfigRepository()->countAssignedWorkflows($context->getCurrentRefId()) > 1) {
            $components[] = $this->ui_factory->messageBox()->info(
                $this->container->translator()->txt('msg_multiple_workflows_assigned')
            )->withButtons([
                $remove_button
            ]);
        }

        $components[] = $form;

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $tool_factory->tool($identification)
                            ->withTitle($title)
                            ->withContentWrapper(function () use ($components) {
                                return $this->ui_factory->legacy(
                                    $this->ui_renderer->render(
                                        $components
                                    )
                                );
                            });
    }

    protected function hasTool(Context $context) : bool
    {
        // check if context is valid
        $types = $this->workflow_config->getActivatedForTypes();
        $context_type = $context->getContextType();
        if (!in_array($context_type, $types, true)) {
            return false;
        }
        // ask the container
        if (!$this->workflow_container->isToolAvailable($context)) {
            return false;
        }

        if (!$this->container->objectInfoProvider()->isOnMembersTab($context->getCurrentRefId())) {
            return false;
        }
        if (!$context->canUserAdministrateMembers()) {
            return false;
        }

        return true;
    }
}
