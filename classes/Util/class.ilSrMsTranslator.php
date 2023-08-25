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

declare(strict_types=1);

use srag\Plugins\SrMemberships\Translator;

class ilSrMsTranslator implements Translator
{
    public function txt(string $key) : string
    {
        return $this->resolvePlugin()->txt($key);
    }

    /** @noinspection PhpUndefinedClassInspection */
    private function resolvePlugin() : ilSrMembershipsPlugin
    {
        static $plugin;
        if (!isset($plugin)) {
            global $DIC;
            if (isset($DIC['component.factory'])) {
                /** @var ilComponentFactory $component_factory */
                $component_factory = $DIC['component.factory'];
                return $plugin = $component_factory->getPlugin('srmem');
            }
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return $plugin = ilPluginAdmin::getPluginObject(
                IL_COMP_SERVICE,
                "Cron",
                "crnhk",
                ilSrMembershipsPlugin::PLUGIN_NAME
            );
        }
        return $plugin;
    }
}
