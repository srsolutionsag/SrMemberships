<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\Mode;

use ILIAS\UI\Factory;
use srag\Plugins\SrMemberships\Translator;
use ILIAS\UI\Component\Input\Field\Section;
use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\TrafoGenerator;

/**
 * @author      Fabian Schmid <fabian@sr.solutions>
 */
abstract class BaseForm
{
    use TrafoGenerator;
    protected WorkflowContainer $workflow_container;
    protected Context $context;

    protected ObjectModeRepository $repository;
    protected Modes $possible_modes;
    protected Translator $translator;
    protected Factory $ui_factory;

    public function __construct(
        WorkflowContainer $workflow_container,
        Context $context,
        Container $container
    ) {
        $this->workflow_container = $workflow_container;
        $this->context = $context;
        $this->possible_modes = $this->readPossibleModes($this->workflow_container);
        $this->ui_factory = $container->dic()->ui()->factory();
        $this->translator = $container->translator();
        $this->repository = $container->objectModeRepository();
    }

    abstract protected function checkModes(Modes $modes): Modes;

    abstract protected function readPossibleModes(WorkflowContainer $workflow_container): Modes;

    abstract protected function getHeader(): string;

    abstract protected function getFields(): array;

    public function getFormSection(): Section
    {
        return $this->ui_factory->input()->field()->section(
            $this->getFields(),
            $this->translator->txt($this->getHeader())
        );
    }
}
