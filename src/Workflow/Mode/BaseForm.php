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

    /**
     * @var \srag\Plugins\SrMemberships\Workflow\Mode\ObjectModeRepository
     */
    protected $repository;
    /**
     * @var WorkflowContainer
     */
    protected $workflow_container;
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var Modes
     */
    protected $possible_modes;
    /**
     * @var \srag\Plugins\SrMemberships\Translator
     */
    protected $translator;
    /**
     * @var \ILIAS\UI\Factory
     */
    protected $ui_factory;

    public function __construct(
        WorkflowContainer $workflow_container,
        Context $context,
        Container $container
    ) {
        $this->context = $context;
        $this->workflow_container = $workflow_container;
        $this->possible_modes = $this->readPossibleModes($workflow_container);
        $this->ui_factory = $container->dic()->ui()->factory();
        $this->translator = $container->translator();
        $this->repository = $container->objectModeRepository();
    }

    abstract protected function checkModes(Modes $modes) : Modes;

    abstract protected function readPossibleModes(WorkflowContainer $workflow_container) : Modes;

    abstract protected function getHeader() : string;

    abstract protected function getFields() : array;

    public function getFormSection() : Section
    {
        return $this->ui_factory->input()->field()->section(
            $this->getFields(),
            $this->translator->txt($this->getHeader())
        );
    }
}
