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

use srag\Plugins\SrMemberships\Workflow\ByRoleSync\Config\Form;
use srag\Plugins\SrMemberships\Config\General\GeneralConfig;

class ilSrMsByRoleSyncConfigurationGUI extends ilSrMsAbstractGUI
{
    public const CMD_SAVE = 'save';

    /**
     * @var Form
     */
    private $form;

    public function __construct()
    {
        parent::__construct();
        $this->form = new Form(
            $this,
            self::CMD_SAVE,
            $this->config->byRoleSync(),
            $this->container
        );
    }

    protected function index(): void
    {
        $this->render(
            $this->form->getForm()
        );
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

        $tabs->addConfigurationTab(true, GeneralConfig::BY_ROLE_SYNC);
    }

    protected function canUserExecute(ilSrMsAccessHandler $access_handler, string $command): bool
    {
        return $access_handler->isAdministrator();
    }
}
