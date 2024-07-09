<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\Mode\Run;

use InvalidArgumentException;
use srag\Plugins\SrMemberships\Workflow\Mode\Modes;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Workflow\Mode\BaseForm;
use ILIAS\UI\Component\Input\Field\Section;

/**
 * @author      Fabian Schmid <fabian@sr.solutions>
 */
class Form extends BaseForm
{
    protected function getFields(): array
    {
        $options = [];
        $bylines = [];
        foreach ($this->possible_modes->getModes() as $mode) {
            $title = $this->translator->txt(strtolower($mode->getModeTitle()));
            $options[(string) $mode->getModeId()] = $title;
            $bylines[(string) $mode->getModeId()] = $title . ': ' . $this->translator->txt(
                strtolower($mode->getModeTitle()) . '_byline'
            );
        }

        $multi_select = $this->ui_factory->input()->field()->multiSelect(
            $this->translator->txt("run_mode"),
            $options,
            implode('<br><br>', $bylines)
        );

        // set value
        $selected_modes = $this->repository->getRunModes(
            $this->context->getCurrentRefId(),
            $this->workflow_container
        );
        $selected_modes = $selected_modes ?: new RunModes();
        $multi_select = $multi_select->withValue(
            $selected_modes->__toArray()
        );

        // store value
        $multi_select = $multi_select->withAdditionalTransformation(
            $this->trafo(function ($value): ?RunModes {
                if (!is_array($value)) {
                    return null;
                }
                $modes = [];
                foreach ($value as $mode) {
                    if (is_numeric($mode)) {
                        $modes[] = RunModes::generic((int) $mode, true);
                    }
                }
                $run_modes = new RunModes(...$modes);
                $this->repository->storeRunModes(
                    $this->context->getCurrentRefId(),
                    $this->workflow_container,
                    $run_modes
                );
                return $run_modes;
            })
        );

        return [
            $multi_select
        ];
    }

    /**  */
    public function getFormSection(): Section
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getFormSection()->withAdditionalTransformation(
            $this->trafo(function ($value) {
                if ($value === null || !isset($value[0]) || $value[0] === null) {
                    $this->repository->storeRunModes(
                        $this->context->getCurrentRefId(),
                        $this->workflow_container,
                        new RunModes()
                    );
                }
                return $value;
            })
        );
    }

    protected function checkModes(Modes $modes): Modes
    {
        if (!$modes instanceof RunModes) {
            throw new InvalidArgumentException("Modes must be of type RunModes");
        }
        return $modes;
    }

    protected function readPossibleModes(WorkflowContainer $workflow_container): Modes
    {
        return $this->checkModes($workflow_container->getPossiblesRunModes());
    }

    protected function getHeader(): string
    {
        return 'run_modes';
    }
}
