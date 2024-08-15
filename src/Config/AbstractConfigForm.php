<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Config;

use srag\Plugins\SrMemberships\Translator;
use ilSrMsAbstractGUI;
use ILIAS\UI\Component\Input\Field\Group;
use ILIAS\UI\Factory;
use srag\Plugins\SrMemberships\Container\Container;
use ILIAS\UI\Implementation\Component\Input\Field\Text;
use ILIAS\UI\Component\Input\Field\Checkbox;
use ILIAS\Refinery\Transformation;
use ILIAS\UI\Component\Input\Field\Select;
use ILIAS\UI\Component\Input\Field\MultiSelect;
use Psr\Http\Message\ServerRequestInterface;
use ILIAS\UI\Component\Input\Container\Form\Standard;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
abstract class AbstractConfigForm implements ConfigForm
{
    /**
     * @readonly
     */
    private ilSrMsAbstractGUI $target_gui;
    /**
     * @readonly
     */
    private string $target_command;
    protected Config $config;
    protected Container $container;
    protected Translator $translator;
    /**
     * @var \ilCtrl
     */
    protected \ilCtrlInterface $ctrl;
    protected \ILIAS\Refinery\Factory $refinery;
    protected Factory $ui_factory;

    public function __construct(
        ilSrMsAbstractGUI $target_gui,
        string $target_command,
        Config $config,
        Container $container
    ) {
        $this->target_gui = $target_gui;
        $this->target_command = $target_command;
        $this->config = $config;
        $this->container = $container;
        $this->translator = $this->container->translator();
        $this->ui_factory = $this->container->dic()->ui()->factory();
        $this->ctrl = $this->container->dic()->ctrl();
        $this->refinery = $this->container->dic()->refinery();
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

    public function getAllOrMultiSelect(
        string $config_key,
        string $label,
        string $all_label,
        int $all_value,
        array $options,
        string $byline = null
    ): Group {
        $value = $this->config->get($config_key, null);
        $group_value = in_array($all_value, $value ?? [], true) ? self::GROUP_KEY_ALL : self::GROUP_KEY_SELECT;

        if (is_array($value)) {
            $value = array_intersect($value, array_keys($options));
        }

        $factory = $this->ui_factory->input()->field();

        $group_fields = [
            self::GROUP_KEY_ALL => $factory->group(
                [],
                $all_label
            ),
            self::GROUP_KEY_SELECT => $factory->group(
                [
                    $factory->multiSelect($label, $options, $byline)
                            ->withValue($group_value === self::GROUP_KEY_SELECT ? $value : null)
                ],
                $this->translator->txt(self::GROUP_KEY_SELECT)
            )
        ];
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $factory
            ->switchableGroup($group_fields, $label)
            ->withValue($group_value)
            ->withAdditionalTransformation(
                $this->refinery->custom()->transformation(function ($value) use ($config_key, $all_value) {
                    if ($value[0] === ConfigForm::GROUP_KEY_ALL) {
                        $this->config->set($config_key, [$all_value]);
                    } else {
                        $this->config->set($config_key, $value[1][0]);
                    }
                    return $value;
                })
            );
    }

    protected function getMultiSelect(
        string $config_key,
        string $label,
        array $options,
        string $byline = null
    ): MultiSelect {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        $value = $this->config->get($config_key, null);
        if (is_array($value)) {
            $value = array_intersect($value, array_keys($options));
        }

        return $this->ui_factory->input()->field()->multiSelect($label, $options, $byline)
                                ->withValue($value)
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

    public function getForm(
        ?ServerRequestInterface $with_request = null
    ): Standard {
        $post_url = $this->ctrl->getLinkTarget($this->target_gui, $this->target_command);

        $standard = $this->ui_factory->input()->container()->form()->standard(
            $post_url,
            $this->getFields()
        );
        if ($with_request instanceof ServerRequestInterface) {
            return $standard->withRequest($with_request);
        }

        return $standard;
    }

    protected function getSelectableRoles(): array
    {
        return $this->container->objectInfoProvider()->getGlobalAndLocalRoles();
    }
}
