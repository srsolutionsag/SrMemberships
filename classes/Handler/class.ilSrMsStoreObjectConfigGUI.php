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

use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use Psr\Http\Message\ServerRequestInterface;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Workflow\Mode\Sync\SyncModes;
use srag\Plugins\SrMemberships\Workflow\Mode\Run\StandardRunModes;
use srag\Plugins\SrMemberships\Workflow\Mode\Run\NullRunModes;

/**
 * Class ilSrMsStoreObjectConfigGUI
 *
 * @ilCtrl_isCalledBy ilSrMsStoreObjectConfigGUI: ilUIPluginRouterGUI
 */
class ilSrMsStoreObjectConfigGUI extends ilSrMsAbstractWorkflowProcessorGUI
{
    protected function setupGlobalTemplate(ilGlobalTemplateInterface $template, ilSrMsTabManager $tabs) : void
    {
        parent::setupGlobalTemplate($template, $tabs);
        $template->setTitle($this->translator->txt('store_object_config'));
    }

    protected function canUserExecute(ilSrMsAccessHandler $access_handler, string $command) : bool
    {
        return true;
    }

    protected function handleWorkflow(
        WorkflowContainer $workflow_container,
        ServerRequestInterface $request,
        Context $context
    ) : void {
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
