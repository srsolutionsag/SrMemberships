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

use srag\Plugins\SrMemberships\Provider\Tool\CollectedMainBarProvider;
use srag\Plugins\SrMemberships\Container\Init;

/** @noRector */
require_once(__DIR__ . '/../vendor/autoload.php');

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ilSrMembershipsPlugin extends ilCronHookPlugin
{
    public const PLUGIN_NAME = 'SrMemberships';

    public function __construct()
    {
        parent::__construct();
        $this->init(); // we must double init the plugin to have provider_collection available
    }

    protected function init() : void
    {
        if ($this->provider_collection === null) {
            return;
        }
        if ($this->isPluginActive()) {
            // $this->updateLanguages(); // FSX TODO remove
            global $DIC;
            $container = Init::init($DIC, $this);
            $dynamic_tool_provider = new CollectedMainBarProvider($container->dic(), $container->plugin());
            $dynamic_tool_provider->init($container);

            $this->provider_collection->setToolProvider($dynamic_tool_provider);

            // Put form labels to 100% width
            $container->dic()->globalScreen()->layout()->meta()->addInlineCss(
                '.il-maincontrols-slate-content .il-standard-form .col-sm-3, .il-maincontrols-slate-content .il-standard-form .col-sm-4 { width:100%; text-align:left; }'
                . '.il-maincontrols-slate-content .il-standard-form .col-sm-9 { width:100%; }'
            // . '.il-maincontrols-slate-content li { padding: 10px 0px !important; }'
            );
        }
    }

    private function isPluginActive() : bool
    {
        // if parent has method isActive, we use this, otherwise we use getActive
        if (method_exists(get_parent_class($this), 'isActive')) {
            return parent::isActive();
        }
        return $this->getActive();
    }

    // we must get a copatible signature with and without string as return type to be compatible with both versions of ILIAS

    public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }

    public function getCronJobInstances() : array
    {
        return [
            new ilSrMembershipsWorkflowJob($this)
        ];
    }

    public function getCronJobInstance($a_job_id) : ilCronJob
    {
        /** @noinspection DegradedSwitchInspection */
        switch ($a_job_id) {
            case ilSrMembershipsWorkflowJob::SRMS_WORKFLOW_JOB:
                return new ilSrMembershipsWorkflowJob($this);
            default:
                throw new OutOfBoundsException("Unknown job id $a_job_id");
        }
    }
}
