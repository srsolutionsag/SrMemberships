<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use srag\Plugins\SrMemberships\Config\AbstractConfigForm;
use srag\Plugins\SrMemberships\Container\Init;
use srag\Plugins\SrMemberships\Config\Configs;
use srag\Plugins\SrMemberships\Container\Container;

abstract class ilSrMsBaseConfigurationGUI extends ilSrMsAbstractGUI
{
    public const CMD_SAVE = 'save';

    public function __construct(protected AbstractConfigForm $form)
    {
        parent::__construct();
    }

    protected function index(): void
    {
        $this->render(
            $this->form->getForm()
        );
    }

    protected function config(): Configs
    {
        return $this->container()->config();
    }

    protected function container(): Container
    {
        if (!$this->container instanceof Container) {
            global $DIC;
            return Init::init($DIC);
        }
        return $this->container;
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

        $tabs->addConfigurationTab(true, $this->getSubTabId());
    }

    protected function canUserExecute(ilSrMsAccessHandler $access_handler, string $command): bool
    {
        return $access_handler->isAdministrator();
    }

    /**
     * @return string
     */
    abstract protected function getSubTabId(): string;
}
