<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow;

use ilSrMsAbstractWorkflowProcessorGUI;
use ilUIPluginRouterGUI;
use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Workflow\ToolObjectConfig\ToolConfigFormHandler;
use ILIAS\UI\Component\Input\Container\Form\Standard;
use ilSrMsAbstractGUI;
use srag\Plugins\SrMemberships\Workflow\Mode\Sync\Form as SyncForm;
use srag\Plugins\SrMemberships\Workflow\Mode\Run\Form as RunForm;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class WorkflowFormBuilder
{
    public function __construct(private readonly Container $container)
    {
    }

    public function getForm(
        Context $context,
        WorkflowContainer $workflow_container
    ): Standard {
        // Prepare URL Building
        $workflow_id = $workflow_container->getWorkflowID();
        $ctrl = $this->container->dic()->ctrl();
        $processor = $workflow_container->getWorkflowToolFormProcessor();

        $ctrl->setParameter(
            $processor,
            ilSrMsAbstractWorkflowProcessorGUI::FALLBACK_REF_ID,
            $context->getCurrentRefId()
        );
        $ctrl->setParameter(
            $processor,
            ilSrMsAbstractWorkflowProcessorGUI::WORKFLOW_CONTAINER_ID,
            $workflow_id
        );

        $post_url = $this->container->dic()->ctrl()->getFormActionByClass(
            [ilUIPluginRouterGUI::class, $processor::class],
            ilSrMsAbstractGUI::CMD_INDEX
        );

        // Tools Form
        $tools_form_handler = new ToolConfigFormHandler(
            $this->container,
            $context,
            $workflow_container
        );

        $tools_form_section = $workflow_container->getWorkflowToolForm()->getFormSection($context);
        $tools_form_section = $tools_form_handler->makeFormSectionStorable($tools_form_section);

        // Sync Modes
        $sync_modes_form = new SyncForm(
            $workflow_container,
            $context,
            $this->container
        );

        // Run Modes
        $run_modes_form = new RunForm(
            $workflow_container,
            $context,
            $this->container
        );

        // Build Form
        return $this->container->dic()->ui()->factory()->input()->container()->form()->standard(
            $post_url,
            [
                $tools_form_section,
                $sync_modes_form->getFormSection(),
                $run_modes_form->getFormSection()
            ]
        );
    }
}
