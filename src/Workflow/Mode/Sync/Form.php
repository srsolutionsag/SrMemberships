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
 */

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\Mode\Sync;

use srag\Plugins\SrMemberships\Workflow\Mode\Modes;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Workflow\Mode\BaseForm;

/**
 * @author      Fabian Schmid <fabian@sr.solutions>
 */
class Form extends BaseForm
{
    protected function getFields() : array
    {
        $radio = $this->ui_factory->input()->field()->radio(
            $this->translator->txt("sync_mode")
        );

        foreach ($this->possible_modes->getModes() as $mode) {
            $radio = $radio->withOption(
                (string) $mode->getModeId(),
                $this->translator->txt(strtolower($mode->getModeTitle())),
                $this->translator->txt(strtolower($mode->getModeTitle()) . '_byline'),
            );
        }

        // get current value or default
        $selected_mode = $this->repository->getSyncMode(
            $this->context->getCurrentRefId(),
            $this->workflow_container
        );

        // if no mode is selected, use default, otherwise selected
        $selected_mode = $selected_mode ?: $this->possible_modes->getDefaultMode();
        $radio = $radio->withValue((string) $selected_mode->getModeId());

        // store value
        $radio = $radio->withAdditionalTransformation(
            $this->trafo(function ($value) {
                $mode = SyncModes::generic((int) $value, true);
                $this->repository->storeSyncMode(
                    $this->context->getCurrentRefId(),
                    $this->workflow_container,
                    $mode
                );
                return $mode;
            })
        );

        // set required
        $radio = $radio->withRequired(true);

        return [
            $radio
        ];
    }

    protected function readPossibleModes(WorkflowContainer $workflow_container) : Modes
    {
        return $this->checkModes($workflow_container->getPossiblesSyncModes());
    }

    protected function checkModes(Modes $modes) : Modes
    {
        if (!$modes instanceof SyncModes) {
            throw new \InvalidArgumentException("Modes must be of type SyncModes");
        }
        return $modes;
    }

    protected function getHeader() : string
    {
        return 'sync_modes';
    }
}
