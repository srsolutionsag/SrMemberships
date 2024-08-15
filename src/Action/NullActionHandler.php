<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Action;

use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Workflow\Mode\Sync\SyncModes;
use srag\Plugins\SrMemberships\Workflow\Mode\Run\RunModes;
use srag\Plugins\SrMemberships\Person\Persons\PersonList;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class NullActionHandler implements ActionHandler
{
    public function newUser(array $data): \ilObjUser
    {
        return new \ilObjUser();
    }

    public function getNotFoundPersonsList(WorkflowContainer $workflow_container, Context $context): PersonList
    {
        return new PersonList();
    }

    public function performActions(
        WorkflowContainer $workflow_container,
        Context $context,
        SyncModes $sync_modes,
        RunModes $run_modes
    ): Summary {
        return Summary::empty();
    }

    public function getRawData(WorkflowContainer $workflow_container, Context $context): array
    {
        return [];
    }

    public function getDeleteWorkflowURL(WorkflowContainer $workflow_container): string
    {
        return '';
    }
}
