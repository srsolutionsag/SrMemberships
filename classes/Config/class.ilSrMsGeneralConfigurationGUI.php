<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Config\General\GeneralConfigForm;

class ilSrMsGeneralConfigurationGUI extends ilSrMsAbstractGUI
{
    public const CMD_SAVE = 'save';
    public const PARAM_WORKFLOW = 'workflow';
    public const CMD_TRIAGE_WORKFLOW = 'triageWorkflow';

    /**
     * @readonly
     */
    private GeneralConfigForm $form;

    public function __construct()
    {
        parent::__construct();
        $this->form = new GeneralConfigForm(
            $this,
            self::CMD_SAVE,
            $this->config->general(),
            $this->container
        );
    }

    protected function index(): void
    {
        $this->render(
            $this->form->getForm()
        );
    }

    protected function triageWorkflow(): void
    {
        $workflow_id = $this->request->getQueryParams()[self::PARAM_WORKFLOW];

        $workflow_container = $this->workflows->getEnabledWorkflowById($workflow_id);
        if ($workflow_container instanceof WorkflowContainer) {
            $this->ctrl->redirect(
                $workflow_container->getConfigClass(),
                ilSrMsAbstractGUI::CMD_INDEX
            );
        }

        $this->render($this->ui_factory->legacy($workflow_id));
    }

    protected function save(): void
    {
        $sent_form = $this->form->getForm()->withRequest($this->request);
        if ($sent_form->getData() === null) {
            $this->render($sent_form);
            return;
        }
        $this->cancel();
    }

    protected function setupGlobalTemplate(
        ilGlobalTemplateInterface $template,
        ilSrMsTabManager $tabs
    ): void {
        $template->setTitle($this->translator->txt("general_configuration"));

        $tabs->addConfigurationTab(true);
    }

    protected function canUserExecute(ilSrMsAccessHandler $access_handler, string $command): bool
    {
        return $access_handler->isAdministrator();
    }
}
