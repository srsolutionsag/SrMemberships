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

namespace srag\Plugins\SrMemberships\Workflow\Mode;

use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use ILIAS\UI\Component\Input\Field\Section;
use srag\Plugins\SrMemberships\Container;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ModesFormHandler
{
    /**
     * @var Context
     */
    private $context;
    /**
     * @var Container
     */
    private $container;
    /**
     * @var WorkflowContainer
     */
    protected $workflow_container;
    /**
     * @var ObjectModeRepository
     */
    protected $repository;

    public function __construct(
        Container $container,
        Context $context,
        WorkflowContainer $workflow_container
    ) {
        $this->context = $context;
        $this->workflow_container = $workflow_container;
        $this->repository = $container->objectModeRepository();
        $this->container = $container;
    }

    public function getFormSection(): Section
    {
        $modes = $this->workflow_container->getPossiblesModes();
        $possible_modes = $modes->getModesAsStrings(
            $this->container->translator(), true
        );
        $ui_factory = $this->container->dic()->ui()->factory();

        $selected_modes = $this->repository->get(
            $this->context->getCurrentRefId(),
            $this->workflow_container
        )->getSelectableIntersectedModeIds($modes);

        $section = $ui_factory->input()->field()->section(
            [
                $ui_factory->input()->field()->multiSelect(
                    $this->container->translator()->txt('workflow_mode_selection'),
                    $possible_modes,
                    $this->container->translator()->txt('workflow_mode_selection_info')
                )->withRequired(false)
                           ->withValue($selected_modes)
            ],
            $this->container->translator()->txt('workflow_mode')
        )->withAdditionalTransformation(
            $this->container->dic()->refinery()->custom()->transformation(function ($value) {
                if ($value[0] === null) {
                    $this->container->objectModeRepository()->clear(
                        $this->context->getCurrentRefId(),
                        $this->workflow_container
                    );
                } elseif (is_array($value[0])) {
                    $this->container->objectModeRepository()->storeFromArrayOfModeIds(
                        $this->context->getCurrentRefId(),
                        $this->workflow_container,
                        $value[0]
                    );
                }
                return $value;
            })
        );
        /** @var Section $section */
        return $section;
    }

}
