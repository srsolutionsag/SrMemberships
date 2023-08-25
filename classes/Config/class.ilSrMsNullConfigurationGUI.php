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
use srag\Plugins\SrMemberships\Config\NullConfigForm;

class ilSrMsNullConfigurationGUI extends ilSrMsAbstractGUI
{
    public const CMD_SAVE = 'save';

    /**
     * @var Form
     */
    private $form;

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

    protected function index() : void
    {
        $this->render($this->form->getForm());
    }

    protected function setupGlobalTemplate(
        ilGlobalTemplateInterface $template,
        ilSrMsTabManager $tabs
    ) : void {
    }

    protected function canUserExecute(ilSrMsAccessHandler $access_handler, string $command) : bool
    {
        return false;
    }
}
