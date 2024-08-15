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

use srag\Plugins\SrMemberships\Translator;
use ilSrMsAbstractGUI;
use ILIAS\UI\Factory;
use srag\Plugins\SrMemberships\Container\Container;
use ILIAS\UI\Implementation\Component\Input\Field\Text;
use ILIAS\UI\Component\Input\Field\Checkbox;
use ILIAS\Refinery\Transformation;
use ILIAS\UI\Component\Input\Field\Select;
use ILIAS\UI\Component\Input\Field\MultiSelect;
use ILIAS\UI\Component\Input\Container\Form\Standard;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
abstract class AbstractForm
{
    /**
     * @readonly
     */
    private ilSrMsAbstractGUI $target_gui;
    /**
     * @readonly
     */
    private string $target_command;
    protected Translator $translator;
    /**
     * @var \ilCtrl
     */
    protected \ilCtrlInterface $ctrl;
    protected \ILIAS\Refinery\Factory $refinery;
    protected Factory $ui_factory;
    /**
     * @var Config
     */
    protected $config;

    public function __construct(
        ilSrMsAbstractGUI $target_gui,
        string $target_command,
        Container $container
    ) {
        $this->target_gui = $target_gui;
        $this->target_command = $target_command;
        $this->translator = $container->translator();
        $this->ui_factory = $container->dic()->ui()->factory();
        $this->ctrl = $container->dic()->ctrl();
        $this->refinery = $container->dic()->refinery();
    }

    protected function getTextInput(
        string $config_key,
        string $label,
        string $byline = null
    ): Text {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->ui_factory->input()->field()->text($label, $byline)
                                ->withValue($this->config->get($config_key, ''))
                                ->withAdditionalTransformation(
                                    $this->getTransformation($config_key)
                                );
    }

    protected function getCheckbox(
        string $config_key,
        string $label,
        string $byline = null
    ): Checkbox {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->ui_factory->input()->field()->checkbox($label, $byline)
                                ->withValue($this->config->get($config_key, false))
                                ->withAdditionalTransformation(
                                    $this->getTransformation($config_key)
                                );
    }

    protected function getSelect(
        string $config_key,
        string $label,
        array $options,
        string $byline = null
    ): Select {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->ui_factory->input()->field()->select($label, $options, $byline)
                                ->withValue($this->config->get($config_key, null))
                                ->withAdditionalTransformation(
                                    $this->getTransformation($config_key)
                                );
    }

    protected function getMultiSelect(
        string $config_key,
        string $label,
        array $options,
        string $byline = null
    ): MultiSelect {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->ui_factory->input()->field()->multiSelect($label, $options, $byline)
                                ->withValue($this->config->get($config_key, null))
                                ->withAdditionalTransformation(
                                    $this->getTransformation($config_key)
                                );
    }

    protected function getTransformation(string $config_key): Transformation
    {
        return $this->refinery->custom()->transformation(function ($value) use ($config_key) {
            $this->config->set($config_key, $value);
            return $value;
        });
    }

    abstract protected function getFields(): array;

    public function getForm(): Standard
    {
        $post_url = $this->ctrl->getLinkTarget($this->target_gui, $this->target_command);

        return $this->ui_factory->input()->container()->form()->standard(
            $post_url,
            $this->getFields()
        );
    }
}
