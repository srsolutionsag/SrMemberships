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

/**
 * Class ilSrMembershipsHandlerGUI
 *
 * @ilCtrl_isCalledBy ilSrMembershipsHandlerGUI: ilUIPluginRouterGUI
 */
class ilSrMembershipsHandlerGUI extends ilSrMsAbstractGUI
{
    public const CMD_SAVE = 'save';

    protected function setupGlobalTemplate(ilGlobalTemplateInterface $template, ilSrMsTabManager $tabs) : void
    {
        $template->setTitle($this->translator->txt("handle_workflow_screen"));

        $ref_id = $this->container->dic()->http()->request()->getQueryParams()["fallback_ref_id"] ?? 1;
        $tabs->addBackToObject($ref_id);
    }

    protected function canUserExecute(ilSrMsAccessHandler $access_handler, string $command) : bool
    {
        return true;
    }

    protected function index() : void
    {
        $this->render(
            $this->ui_factory->legacy('<pre>' . print_r($_POST, true) . '</pre>')
        );
    }
}
