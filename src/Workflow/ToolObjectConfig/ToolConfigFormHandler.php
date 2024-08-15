<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\ToolObjectConfig;

use Throwable;
use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use ILIAS\UI\Component\Input\Field\Section;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ToolConfigFormHandler
{
    protected Context $context;
    protected WorkflowContainer $workflow_container;
    /**
     * @readonly
     */
    private Container $container;
    /**
     * @readonly
     */
    private ToolObjectConfigRepository $repository;

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

    public function makeFormSectionStorable(Section $section): Section
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
        } catch (Throwable $exception) {
        }
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $section;
    }
}
