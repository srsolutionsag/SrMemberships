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

use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Container\Init;
use srag\Plugins\SrMemberships\Workflow\Mode\Sync\SyncModes;

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
class ilSrMembershipsWorkflowJob extends ilCronJob
{
    public const SRMS_WORKFLOW_JOB = "srms_workflow_job";
    /**
     * @var Container
     */
    private $container;

    public function __construct(ilSrMembershipsPlugin $plugin)
    {
        global $DIC;
        $this->container = Init::init($DIC, $plugin);
    }

    public function getTitle() : string
    {
        return "SRMS Workflow Job";
    }

    public function getDescription() : string
    {
        return "This job will run all workflows that are configured to run via cron.";
    }

    public function getId() : string
    {
        return self::SRMS_WORKFLOW_JOB;
    }

    public function hasAutoActivation() : bool
    {
        return true;
    }

    public function hasFlexibleSchedule() : bool
    {
        return true;
    }

    public function getDefaultScheduleType() : int
    {
        return ilCronJob::SCHEDULE_TYPE_IN_HOURS;
    }

    public function getDefaultScheduleValue() : ?int
    {
        return 6;
    }

    public function run() : ilCronJobResult
    {
        $result = new ilCronJobResult();

        $workflows = $this->container->workflows()->getEnabledWorkflows();
        foreach ($workflows as $workflow) {
            if (!$workflow->getPossiblesRunModes()->isRunAsCron()) {
                continue;
            }
            // Get all assigned objects of this workflow
            foreach ($this->container->toolObjectConfigRepository()->getAssignedRefIds($workflow) as $ref_id) {
                $context = $this->container->contextFactory()->get($ref_id, $this->container->dic()->user()->getId());

                $sync_modes = new SyncModes(
                    $this->container->objectModeRepository()->getSyncMode($ref_id, $workflow)
                );
                $run_modes = $this->container->objectModeRepository()->getRunModes($ref_id, $workflow);

                if ($run_modes === null) {
                    continue;
                }
                try {
                    $summary = $workflow->getActionHandler($context)->performActions(
                        $workflow,
                        $context,
                        $sync_modes,
                        $run_modes
                    );
                } catch (Throwable $e) {
                    $result->setMessage($result->getMessage() . "\n" . $e->getMessage());
                }
            }
        }
        $result->setStatus(ilCronJobResult::STATUS_OK);

        return $result;
    }
}
