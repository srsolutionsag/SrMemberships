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

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\ToolObjectConfig;

use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use ILIAS\UI\Component\Input\Field\Section;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ToolConfigFormHandler
{
    /**
     * @var \srag\Plugins\SrMemberships\Container\Container
     */
    private $container;
    /**
     * @var ToolObjectConfigRepository
     */
    private $repository;

    /**
     * @var WorkflowContainer
     */
    protected $workflow_container;
    /**
     * @var Context
     */
    protected $context;

    public function __construct(
        Container $container,
        Context $context,
        WorkflowContainer $workflow_container
    ) {
        $this->context = $context;
        $this->workflow_container = $workflow_container;
        $this->repository = $container->toolObjectConfigRepository();
        $this->container = $container;
    }

    public function makeFormSectionStorable(Section $section) : Section
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $section = $section->withAdditionalTransformation(
            $this->container->dic()->refinery()->custom()->transformation(function ($value) {
                $this->repository->store(
                    $this->context->getCurrentRefId(),
                    $this->workflow_container,
                    $value
                );
                return $value;
            })
        );
        try {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $section = $section->withValue(
                $this->repository->get(
                    $this->context->getCurrentRefId(),
                    $this->workflow_container
                )
            );
        } catch (\Throwable $e) {
        }
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $section;
    }
}
