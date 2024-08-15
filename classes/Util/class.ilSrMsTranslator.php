<?php
/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

use srag\Plugins\SrMemberships\Translator;

class ilSrMsTranslator implements Translator
{
    public function txt(string $key): string
    {
        return $this->resolvePlugin()->txt($key);
    }

    /** @noinspection PhpUndefinedClassInspection */
    private function resolvePlugin(): ilSrMembershipsPlugin
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
