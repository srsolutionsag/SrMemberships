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

use srag\Plugins\SrMemberships\Workflow\ByLogin\Config\Form;
use srag\Plugins\SrMemberships\Config\AbstractConfigForm;
use srag\Plugins\SrMemberships\Container\Init;
use srag\Plugins\SrMemberships\Config\Configs;
use srag\Plugins\SrMemberships\Container\Container;

abstract class ilSrMsBaseConfigurationGUI extends ilSrMsAbstractGUI
{
    public const CMD_SAVE = 'save';

    /**
     * @var Form
     */
    protected $form;

    public function __construct(AbstractConfigForm $form)
    {
        parent::__construct();
        $this->form = $form;
    }

    protected function index() : void
    {
        $this->render(
            $this->form->getForm()
        );
    }

    protected function config() : Configs
    {
        return $this->container()->config();
    }

    protected function container() : Container
    {
        if (!isset($this->container)) {
            global $DIC;
            return Init::init($DIC);
        }
        return $this->container;
    }

    protected function save() : void
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
    ) : void {
        $template->setTitle($this->translator->txt("general_configuration"));

        $tabs->addConfigurationTab(true, $this->getSubTabId());
    }

    protected function canUserExecute(ilSrMsAccessHandler $access_handler, string $command) : bool
    {
        return $access_handler->isAdministrator();
    }

    /**
     * @return string
     */
    abstract protected function getSubTabId() : string;
}
