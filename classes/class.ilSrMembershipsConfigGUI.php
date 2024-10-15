<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
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
                ilSrMsAbstractGUI::CMD_INDEX
            );
        }
    }
}
