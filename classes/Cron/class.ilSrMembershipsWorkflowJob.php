<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Workflow\Mode\Mode;
use srag\Plugins\SrMemberships\Workflow\Mode\Modes;
use srag\Plugins\SrMemberships\Workflow\Mode\Sync\SyncModes;
use ILIAS\Cron\Schedule\CronJobScheduleType;

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
     * @readonly
     */
    private Container $container;
    /**
     * @var ilLogger
     */
    private $logger;

    public function __construct(ilSrMembershipsPlugin $plugin)
    {
        global $DIC;
        global $srmembershipsContainer;
        $this->container = $srmembershipsContainer;
        $this->logger = $this->container->dic()->logger()->root();
    }

    public function getTitle(): string
    {
        return "SRMS Workflow Job";
    }

    public function getDescription(): string
    {
        return "This job will run all workflows that are configured to run via cron.";
    }

    public function getId(): string
    {
        return self::SRMS_WORKFLOW_JOB;
    }

    public function hasAutoActivation(): bool
    {
        return true;
    }

    public function hasFlexibleSchedule(): bool
    {
        return true;
    }

    public function getDefaultScheduleType(): int
    {
        return ilCronJob::SCHEDULE_TYPE_IN_HOURS;
    }

    public function getDefaultScheduleValue(): ?int
    {
        return 6;
    }

    public function run(): ilCronJobResult
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

                $mode = $this->container->objectModeRepository()->getSyncMode($ref_id, $workflow);
                if (!$mode instanceof Mode) {
                    continue;
                }
                $sync_modes = new SyncModes(
                    $mode
                );
                $run_modes = $this->container->objectModeRepository()->getRunModes($ref_id, $workflow);

                if (!$run_modes instanceof Modes) {
                    continue;
                }
                try {
                    $summary = $workflow->getActionHandler($context)->performActions(
                        $workflow,
                        $context,
                        $sync_modes,
                        $run_modes
                    );
                    $summary_text = implode(
                        '; ',
                        array_filter(
                            explode(
                                "\n",
                                (string) $summary->getSummary()
                            ),
                            static function ($line): bool {
                                return trim($line) !== ''; // Remove empty lines
                            }
                        )
                    );

                    $this->logger->info('Ref-ID ' . $context->getCurrentRefId() . ': ' . $summary_text);
                } catch (Throwable $e) {
                    $this->logger->info($e->getMessage());
                }
            }
        }
        $result->setStatus(ilCronJobResult::STATUS_OK);

        return $result;
    }
}
