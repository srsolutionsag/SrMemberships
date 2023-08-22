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

namespace srag\Plugins\SrMemberships\Workflow;

use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Workflow\ToolObjectConfig\ToolConfigFormHandler;
use srag\Plugins\SrMemberships\Workflow\Mode\ModesFormHandler;
use ILIAS\UI\Component\Input\Container\Form\Standard;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class WorkflowFormBuilder
{
    /**
     * @var \srag\Plugins\SrMemberships\Container\Container
     */
    private $container;

    public function __construct(
        Container $container
    ) {
        $this->container = $container;
    }

    public function getForm(
        Context $context,
        WorkflowContainer $workflow_container
    ) : Standard {
        // Prepare URL Building
        $workflow_id = $workflow_container->getWorkflowID();
        $ctrl = $this->container->dic()->ctrl();
        $processor = $workflow_container->getWorkflowToolFormProcessor();

        $ctrl->setParameter(
            $processor,
            \ilSrMsAbstractWorkflowProcessorGUI::FALLBACK_REF_ID,
            $context->getCurrentRefId()
        );
        $ctrl->setParameter(
            $processor,
            \ilSrMsAbstractWorkflowProcessorGUI::WORKFLOW_CONTAINER_ID,
            $workflow_id
        );

        $post_url = $this->container->dic()->ctrl()->getFormActionByClass(
            [\ilUIPluginRouterGUI::class, get_class($processor)],
            \ilSrMsAbstractWorkflowProcessorGUI::CMD_INDEX
        );

        // Tools Form
        $tools_form_handler = new ToolConfigFormHandler(
            $this->container,
            $context,
            $workflow_container
        );

        $tools_form_section = $workflow_container->getWorkflowToolForm()->getFormSection($context);
        $tools_form_section = $tools_form_handler->makeFormSectionStorable($tools_form_section);

        // Modes Form
        $modes_form = new ModesFormHandler(
            $this->container,
            $context,
            $workflow_container
        );

        // Build Form
        return $this->container->dic()->ui()->factory()->input()->container()->form()->standard(
            $post_url,
            [
                $tools_form_section,
                $modes_form->getFormSection(),
            ]
        );
    }
}
