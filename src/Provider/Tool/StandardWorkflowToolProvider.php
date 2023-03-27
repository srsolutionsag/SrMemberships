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
use srag\Plugins\SrMemberships\Container;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Config\Config;
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
     * @var Config
     */
    private $general_config;
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
        $this->general_config = $container->config();
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
    ): ?Tool {
        if (!$this->hasTool($context)) {
            return null;
        }
        $workflow_id = $this->workflow_container->getWorkflowID();
        $form = $this->form_builder->getForm($context, $this->workflow_container);

        $title = $this->container->translator()->txt('feature_' . $workflow_id);
        $identification = $identification_factory->identifier($workflow_id);

        return $tool_factory->tool($identification)
                            ->withTitle($title)
                            ->withContentWrapper(function () use ($form) {
                                return $this->ui_factory->legacy(
                                    $this->ui_renderer->render(
                                        $form
                                    )
                                );
                            });
    }

    protected function hasTool(Context $context): bool
    {
        // check if context is valid
        $types = $this->workflow_config->getActivatedForTypes();
        $context_type = $context->getContextType();
        if (!in_array($context_type, $types)) {
            return false;
        }
        if (!$this->container->objectInfoProvider()->isOnMembersTab($context->getCurrentRefId())) {
            return false;
        }
        return true;
    }

}
