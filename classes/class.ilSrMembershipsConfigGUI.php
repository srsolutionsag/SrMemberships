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
 * @ilCtrl_isCalledBy ilSrMembershipsConfigGUI: ilObjComponentSettingsGUI
 */
class ilSrMembershipsConfigGUI extends ilPluginConfigGUI
{
    /**
     * Forwards the request to @param string $cmd
     * @throws ilCtrlException
     */
    public function performCommand(/*string*/ $cmd): void
    {
        global $DIC;

        if (strtolower(ilSrMembershipsDispatcherGUI::class) === $DIC->ctrl()->getNextClass($this)) {
            // forward the request to the plugin dispatcher if it's ilCtrl's
            // next command class, because this means a further command class
            // is already provided.
            $DIC->ctrl()->forwardCommand(new ilSrMembershipsDispatcherGUI());
        } else {
            // whenever ilCtrl's next class is not the plugin dispatcher the
            // request comes from ILIAS (ilAdministrationGUI) itself, in which
            // case the request is redirected to the plugins actual config GUI.
            $DIC->ctrl()->redirectByClass(
                [ilSrMembershipsDispatcherGUI::class, ilSrMsGeneralConfigurationGUI::class],
                ilSrMsGeneralConfigurationGUI::CMD_INDEX
            );
        }
    }
}
