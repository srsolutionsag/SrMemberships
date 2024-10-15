<?php
/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

use srag\Plugins\SrMemberships\Config\Configs;
use srag\Plugins\SrMemberships\Translator;
use srag\Plugins\SrMemberships\Container\Container;

/**
 * This class is responsible for managing the plugin tabs.
 *
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 *
 * This helper class is meant to centralize the tabs implementation
 * and simplify their management. All methods in this class (except
 * any additional getters) should be fluent (return this instance).
 *
 * @noinspection AutoloadingIssuesInspection
 */
class ilSrMsTabManager
{
    // ilSrTabManager tab name and ids:
    public const TAB_CONFIG = 'tab_config_index';
    // ilSrTabManager language variables:
    protected const MSG_BACK_TO = 'msg_back_to';
    public const WORKFLOW_PREFIX = 'workflow_';
    protected Container $container;
    protected Configs $config;
    protected Translator $translator;

    protected ilSrMsAccessHandler $access_handler;

    protected \ilTabsGUI $tabs;

    /**
     * @var ilCtrl
     */
    protected \ilCtrlInterface $ctrl;

    protected int $origin;

    public function __construct(
        Container $container
    ) {
        $this->access_handler = $container->accessHandler();
        $this->translator = $container->translator();
        $this->tabs = $container->dic()->tabs();
        $this->ctrl = $container->dic()->ctrl();
        $this->origin = $container->origin();
        $this->config = $container->config();
        $this->container = $container;
    }

    public function addConfigurationTab(bool $is_active = false, string $activated_feature = null): self
    {
        // add plugin-configuration tab only for administrator
        if (!$this->access_handler->isAdministrator()) {
            return $this;
        }

        $this->tabs->addTab(
            self::TAB_CONFIG,
            $this->translator->txt(self::TAB_CONFIG),
            $this->ctrl->getLinkTargetByClass(
                ilSrMsGeneralConfigurationGUI::class,
                ilSrMsAbstractGUI::CMD_INDEX
            )
        );
        if ($is_active) {
            $this->tabs->addSubTab(
                self::TAB_CONFIG,
                $this->translator->txt(self::TAB_CONFIG),
                $this->ctrl->getLinkTargetByClass(
                    ilSrMsGeneralConfigurationGUI::class,
                    ilSrMsAbstractGUI::CMD_INDEX
                )
            );
            $this->tabs->activateSubTab(self::TAB_CONFIG);
            $this->addFeaturesSubTabs($activated_feature);
        }

        if ($is_active) {
            $this->setActiveTab(self::TAB_CONFIG);
        }

        return $this;
    }

    public function addFeaturesSubTabs(string $active_feature = null): self
    {
        if (!$this->access_handler->isAdministrator()) {
            return $this;
        }

        foreach ($this->config->general()->getEnabledWorkflows() as $enabled_feature) {
            $this->ctrl->setParameterByClass(
                ilSrMsGeneralConfigurationGUI::class,
                ilSrMsGeneralConfigurationGUI::PARAM_WORKFLOW,
                $enabled_feature
            );
            $this->tabs->addSubTab(
                self::WORKFLOW_PREFIX . $enabled_feature,
                $this->translator->txt(self::WORKFLOW_PREFIX . $enabled_feature),
                $this->ctrl->getLinkTargetByClass(
                    ilSrMsGeneralConfigurationGUI::class,
                    ilSrMsGeneralConfigurationGUI::CMD_TRIAGE_WORKFLOW
                )
            );
        }

        if ($active_feature !== null) {
            $this->tabs->activateSubTab(self::WORKFLOW_PREFIX . $active_feature);
        }
        return $this;
    }

    public function addAnotherTab(bool $is_active = false): self
    {
        // add routine-tab only for routine managers.
        //        if (!$this->access_handler->canManageRoutines()) {
        //            return $this;
        //        }
        //
        //        $this->tabs->addTab(
        //            self::TAB_ROUTINES,
        //            $this->translator->txt(self::TAB_ROUTINES),
        //            $this->ctrl->getLinkTargetByClass(
        //                ilSrRoutineGUI::class,
        //                ilSrRoutineGUI::CMD_INDEX
        //            )
        //        );
        //
        //        if ($is_active) {
        //            $this->setActiveTab(self::TAB_ROUTINES);
        //        }

        return $this;
    }

    /**
     * Adds a back-to tab pointing to @return self
     * @see ilSrRoutineGUI::index().
     *
     */
    public function addBackToRoutines(): self
    {
        //        $this->addBackToTarget(
        //            $this->ctrl->getLinkTargetByClass(
        //                ilSrRoutineGUI::class,
        //                ilSrRoutineGUI::CMD_INDEX
        //            )
        //        );

        return $this;
    }

    public function addBackToIndex(string $class): self
    {
        $this->addBackToTarget(
            $this->ctrl->getLinkTargetByClass(
                $class,
                ilSrMsAbstractGUI::CMD_INDEX
            )
        );

        return $this;
    }

    public function addBackToObject(int $ref_id): self
    {
        $this->addBackToTarget(ilLink::_getLink($ref_id));
        return $this;
    }

    public function addBackToObjectMembersTab(int $ref_id): self
    {
        $members_tab_link = $this->container->objectInfoProvider()->getMembersTabLink($ref_id);
        $this->addBackToTarget($members_tab_link);
        return $this;
    }

    public function addBackToTarget(string $target): self
    {
        $this->tabs->setBackTarget(
            $this->translator->txt(self::MSG_BACK_TO),
            $target
        );

        return $this;
    }

    /**
     * Shows a given tab-id as activated (can only be one at a time).
     *
     * @param string $tab_id
     * @return self
     */
    public function setActiveTab(string $tab_id): self
    {
        $this->tabs->activateTab($tab_id);
        return $this;
    }

    /**
     * Deactivates all activated tabs by setting an invalid character as id.
     *
     * @return $this
     */
    public function deactivateTabs(): self
    {
        $this->setActiveTab('§');
        return $this;
    }

    /**
     * Returns whether the current user is in the administration context or not.
     *
     * @return bool
     */
    protected function inAdministration(): bool
    {
        return (ilSrMembershipsDispatcherGUI::ORIGIN_TYPE_ADMINISTRATION === $this->origin);
    }

    /**
     * Returns whether the current user is in the repository context or not.
     *
     * @return bool
     */
    protected function inRepository(): bool
    {
        return (ilSrMembershipsDispatcherGUI::ORIGIN_TYPE_REPOSITORY === $this->origin);
    }
}
