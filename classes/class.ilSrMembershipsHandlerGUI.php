<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

/**
 * Class ilSrMembershipsHandlerGUI
 *
 * @ilCtrl_isCalledBy ilSrMembershipsHandlerGUI: ilUIPluginRouterGUI
 */
class ilSrMembershipsHandlerGUI extends ilSrMsAbstractGUI
{
    public const CMD_SAVE = 'save';

    protected function setupGlobalTemplate(ilGlobalTemplateInterface $template, ilSrMsTabManager $tabs): void
    {
        $template->setTitle($this->translator->txt("handle_workflow_screen"));

        $ref_id = $this->container->dic()->http()->request()->getQueryParams()["fallback_ref_id"] ?? 1;
        $tabs->addBackToObject($ref_id);
    }

    protected function canUserExecute(ilSrMsAccessHandler $access_handler, string $command): bool
    {
        return true;
    }

    protected function index(): void
    {
        $this->render(
            $this->ui_factory->legacy('<pre>' . print_r($_POST, true) . '</pre>')
        );
    }
}
