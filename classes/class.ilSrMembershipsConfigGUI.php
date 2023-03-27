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

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * This is the entry point of the plugin-configuration.
 *
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 *
 * The classes only purpose is, to forward requests to the configuration
 * to the actual implementation: @see ilSrConfigGUI.
 *
 * @noinspection AutoloadingIssuesInspection
 */
class ilSrMembershipsConfigGUI extends ilPluginConfigGUI
{
    /**
     * Forwards the request to @param string $cmd
     * @throws ilCtrlException
     */
    public function performCommand($cmd): void
    {
        global $DIC;

        if (strtolower(ilSrMembershipsDispatcher::class) === $DIC->ctrl()->getNextClass($this)) {
            // forward the request to the plugin dispatcher if it's ilCtrl's
            // next command class, because this means a further command class
            // is already provided.
            $DIC->ctrl()->forwardCommand(new ilSrMembershipsDispatcher());
        } else {
            // whenever ilCtrl's next class is not the plugin dispatcher the
            // request comes from ILIAS (ilAdministrationGUI) itself, in which
            // case the request is redirected to the plugins actual config GUI.
            $DIC->ctrl()->redirectByClass(
                [ilSrMembershipsDispatcher::class, ilSrMsGeneralConfigurationGUI::class],
                ilSrMsGeneralConfigurationGUI::CMD_INDEX
            );
        }
    }
}
