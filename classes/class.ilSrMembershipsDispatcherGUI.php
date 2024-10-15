<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Container\Init;

/**
 * @author            Fabian Schmid <fabian@sr.solutions>
 *
 * @ilCtrl_isCalledBy ilSrMembershipsDispatcherGUI : ilUIPluginRouterGUI
 * @ilCtrl_isCalledBy ilSrMembershipsDispatcherGUI : ilSrMembershipsConfigGUI
 * @ilCtrl_isCalledBy ilSrMembershipsDispatcherGUI : ilObjComponentSettingsGUI
 *
 * @ilCtrl_Calls      ilSrMembershipsDispatcherGUI : ilSrMsGeneralConfigurationGUI
 * @ilCtrl_Calls      ilSrMembershipsDispatcherGUI : ilSrMsByRoleSyncConfigurationGUI
 * @ilCtrl_Calls      ilSrMembershipsDispatcherGUI : ilSrMsByLoginConfigurationGUI
 * @ilCtrl_Calls      ilSrMembershipsDispatcherGUI : ilSrMsByMatriculationConfigurationGUI
 * @ilCtrl_Calls      ilSrMembershipsDispatcherGUI : ilSrMsGeneralUploadHandlerGUI
 */
class ilSrMembershipsDispatcherGUI
{
    public const ORIGIN_TYPE_REPOSITORY = 1;
    public const ORIGIN_TYPE_ADMINISTRATION = 2;
    public const ORIGIN_TYPE_UNKNOWN = 4;
    protected Container $container;
    /**
     * @var ilGlobalTemplateInterface
     */
    protected $global_template;

    /**
     * @var ilCtrl
     */
    protected $ctrl;

    /**
     * Initializes the global template and ilCtrl.
     */
    public function __construct()
    {
        global $DIC;
        $this->global_template = $DIC->ui()->mainTemplate();
        $this->ctrl = $DIC->ctrl();
        $this->container = Init::init($DIC);
    }

    public function executeCommand(): void
    {
        $next_class = $this->ctrl->getNextClass();

        // Dynamically forward to the next class, if it is a workflow type mathing.
        foreach ($this->container->workflows()->getAllWorkflows() as $workflow) {
            $class_name = $workflow->getConfigClass()::class;
            if ($next_class === strtolower($class_name)) {
                $this->safelyForward($class_name);
                return;
            }
        }

        switch ($next_class) {
            case strtolower(ilSrMsGeneralConfigurationGUI::class):
                $this->safelyForward(ilSrMsGeneralConfigurationGUI::class);
                break;
            case strtolower(ilSrMsGeneralUploadHandlerGUI::class):
                $this->safelyForward(ilSrMsGeneralUploadHandlerGUI::class);
                break;
            case strtolower(self::class):
                throw new LogicException(self::class . " MUST never be the executing class.");
                break;
        }

        // if requests have other classes than the ilAdministrationGUI as
        // baseclass, the global template must be printed manually.
        if (self::ORIGIN_TYPE_ADMINISTRATION !== self::getOriginType()) {
            $this->global_template->printToStdout();
        }
    }

    /**
     * Returns the origin-type of the current request.
     *
     * The origin is determined by ilCtrl's call-history, whereas the
     * current baseclass is crucial. The plugin will currently distinguish
     * between the administration and the repository. External origins
     * are not considered here.
     *
     * @return int
     */
    public static function getOriginType(): int
    {
        global $DIC;
        $call_history = $DIC->ctrl()->getCallHistory();
        $base_class = array_shift($call_history);
        $base_class = strtolower((string) ($base_class['class'] ?? $base_class['cmdClass'] ?? ''));

        return match ($base_class) {
            strtolower(ilUIPluginRouterGUI::class) => self::ORIGIN_TYPE_REPOSITORY,
            strtolower(ilAdministrationGUI::class) => self::ORIGIN_TYPE_ADMINISTRATION,
            default => self::ORIGIN_TYPE_UNKNOWN,
        };
    }

    /**
     * Returns a fully qualified link target for the given class and command.
     *
     * This method can be used whenever a link to a command class of this plugin
     * is made from outside ilCtrl's current scope (e.g. MenuProvider)
     *
     * @param string $class
     * @param string $cmd
     * @return string
     */
    public static function getLinkTarget(string $class, string $cmd): string
    {
        global $DIC;

        return $DIC->ctrl()->getLinkTargetByClass(
            [ilUIPluginRouterGUI::class, self::class, $class],
            $cmd
        );
    }

    /**
     * Safely forwards the current request to the given command class.
     *
     * Since this plugin implements GUI classes, that aren't working if certain
     * required GET parameters are missing, they might throw an according
     * LogicException. This method therefore wraps the mechanism and catches
     * possible exceptions to display an on-screen message instead.
     *
     * @param string $class_name
     */
    protected function safelyForward(string $class_name): void
    {
        try {
            $this->ctrl->forwardCommand(new $class_name());
        } catch (Throwable $exception) {
            $this->global_template->setOnScreenMessage(
                'failure',
                ($this->isDebugModeEnabled()) ?
                    $this->getExceptionString($exception) :
                    $exception->getMessage()
            );
        }
    }

    private function isDebugModeEnabled(): bool
    {
        return true; // TODO move to config
    }

    /**
     * Helper function to nicely format the exception message to display on screen.
     *
     * @param Throwable $exception
     * @return string
     */
    protected function getExceptionString(Throwable $exception): string
    {
        $message = "{$exception->getMessage()} : ";
        $message .= "<br /><br />";

        return $message . str_replace(
            PHP_EOL,
            "<br />",
            $exception->getTraceAsString()
        );
    }
}
