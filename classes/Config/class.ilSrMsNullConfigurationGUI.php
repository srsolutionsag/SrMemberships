<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use srag\Plugins\SrMemberships\Workflow\ByRoleSync\Config\Form;
use srag\Plugins\SrMemberships\Config\NullConfigForm;

class ilSrMsNullConfigurationGUI extends ilSrMsAbstractGUI
{
    public const CMD_SAVE = 'save';

    /**
     * @var Form
     * @readonly
     */
    private NullConfigForm $form;

    public function __construct()
    {
        parent::__construct();
        $this->form = new NullConfigForm(
            $this,
            self::CMD_SAVE,
            $this->config->byRoleSync(),
            $this->container
        );
    }

    protected function index(): void
    {
        $this->render($this->form->getForm());
    }

    protected function setupGlobalTemplate(
        ilGlobalTemplateInterface $template,
        ilSrMsTabManager $tabs
    ): void {
    }

    protected function canUserExecute(ilSrMsAccessHandler $access_handler, string $command): bool
    {
        return false;
    }
}
