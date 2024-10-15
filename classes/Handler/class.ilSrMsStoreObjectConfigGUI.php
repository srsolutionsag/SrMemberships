<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use Psr\Http\Message\ServerRequestInterface;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Workflow\Mode\Sync\SyncModes;
use srag\Plugins\SrMemberships\Workflow\Mode\Run\NullRunModes;

/**
 * Class ilSrMsStoreObjectConfigGUI
 *
 * @ilCtrl_isCalledBy ilSrMsStoreObjectConfigGUI: ilUIPluginRouterGUI
 */
class ilSrMsStoreObjectConfigGUI extends ilSrMsAbstractWorkflowProcessorGUI
{
    protected function setupGlobalTemplate(ilGlobalTemplateInterface $template, ilSrMsTabManager $tabs): void
    {
        parent::setupGlobalTemplate($template, $tabs);
        $template->setTitle($this->translator->txt('store_object_config'));
    }

    protected function canUserExecute(ilSrMsAccessHandler $access_handler, string $command): bool
    {
        return true;
    }

    protected function handleWorkflow(
        WorkflowContainer $workflow_container,
        ServerRequestInterface $request,
        Context $context
    ): void {
        $form = $this->form_builder->getForm($context, $workflow_container)->withRequest($request);
        $data = $form->getData();
        if ($data !== null) {
            // Run Action Handler
            $sync_mode = $this->container->objectModeRepository()->getSyncMode(
                $context->getCurrentRefId(),
                $workflow_container
            );
            $sync_modes = new SyncModes($sync_mode);

            $run_modes = $this->container->objectModeRepository()->getRunModes(
                $context->getCurrentRefId(),
                $workflow_container
            ) ?? new NullRunModes();

            $summary = $workflow_container->getActionHandler($context)->performActions(
                $workflow_container,
                $context,
                $sync_modes,
                $run_modes
            );

            switch (true) {
                case $summary->isNull():
                    break;
                case $summary->isOK():
                    $this->sendInfoMessage(nl2br($summary->getSummary()));
                    break;
                case !$summary->isOK():
                    $this->sendErrorMessage(nl2br($summary->getSummary()));
                    break;
            }

            $this->sendSuccessMessage($this->translator->txt('msg_object_config_stored'));
            $this->redirectToRefId($context->getCurrentRefId());
        }
        $this->render($form);
    }
}
