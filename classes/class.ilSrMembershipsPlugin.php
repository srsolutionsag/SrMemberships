<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use srag\Plugins\SrMemberships\Provider\Tool\CollectedMainBarProvider;
use srag\Plugins\SrMemberships\Container\Init;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ilSrMembershipsPlugin extends ilCronHookPlugin
{
    public const PLUGIN_NAME = 'SrMemberships';

    public function __construct(
        ilDBInterface $db,
        ilComponentRepositoryWrite $component_repository,
        string $id
    ) {
        global $srmembershipsContainer;
        parent::__construct($db, $component_repository, $id);
        $GLOBALS['srmembershipsContainer'] = $srmembershipsContainer = Init::init($this, $this->getLanguageHandler());
        $this->afterInit(); // we must double init the plugin to have provider_collection available
    }

    protected function afterInit(): void
    {
        global $DIC;
        global $srmembershipsContainer;
        if ($this->provider_collection === null) {
            return;
        }
        if (isset($DIC['global_screen']) && $this->isActive()) {
            $dynamic_tool_provider = new CollectedMainBarProvider($srmembershipsContainer->dic(), $srmembershipsContainer->plugin());
            $dynamic_tool_provider->init($srmembershipsContainer);

            $this->provider_collection->setToolProvider($dynamic_tool_provider);

            // Put form labels to 100% width
            $srmembershipsContainer->dic()->globalScreen()->layout()->meta()->addInlineCss(
                '.il-maincontrols-slate-content .il-standard-form .col-sm-2, .il-maincontrols-slate-content .il-standard-form .col-sm-4 { width:100%; text-align:left; }'
                . '.il-maincontrols-slate-content .il-standard-form .col-sm-8 { width:100%; }'
                . '.il-maincontrols-slate-content .il-standard-form li, .il-maincontrols-slate-content .il-standard-form .il-input-radiooption { padding: 5px 0px !important; }'
                . '.il-maincontrols-slate-content .il-standard-form .il-section-input-header h2 { padding: 0px 0px !important; }'
                . '.il-maincontrols-slate-content .il-standard-form .il-section-input-header { padding: 0px 10px 0px 10px !important; }'
                . '.il-maincontrols-slate-content .il-standard-form .il-standard-form-header + .il-section-input { margin-top: 0px !important; }'
            );
        }
    }

    private function isPluginActive(): bool
    {
        // if parent has method isActive, we use this, otherwise we use getActive
        if (method_exists(get_parent_class($this), 'isActive')) {
            return parent::isActive();
        }
        return $this->getActive();
    }

    // we must get a copatible signature with and without string as return type to be compatible with both versions of ILIAS

    public function getPluginName(): string
    {
        return self::PLUGIN_NAME;
    }

    public function getCronJobInstances(): array
    {
        return [
            new ilSrMembershipsWorkflowJob($this)
        ];
    }

    public function getCronJobInstance($a_job_id): ilCronJob
    {
        return match ($a_job_id) {
            ilSrMembershipsWorkflowJob::SRMS_WORKFLOW_JOB => new ilSrMembershipsWorkflowJob($this),
            default => throw new OutOfBoundsException("Unknown job id $a_job_id"),
        };
    }

    protected function afterUninstall(): void
    {
        global $DIC;
        global $srmembershipsContainer;
        $container = $srmembershipsContainer;
        $container->dic()->database()->dropTable('srms_config', false);
        $container->dic()->database()->dropTable('srms_object_config', false);
        $container->dic()->database()->dropTable('srms_object_mode', false);
    }
}
