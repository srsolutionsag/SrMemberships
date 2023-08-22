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

namespace srag\Plugins\SrMemberships\Config;

use ILIAS\UI\Factory;
use srag\Plugins\SrMemberships\Container\Container;
use ILIAS\UI\Implementation\Component\Input\Field\Text;
use ILIAS\UI\Component\Input\Field\Checkbox;
use ILIAS\Refinery\Transformation;
use ILIAS\UI\Component\Input\Field\Select;
use ILIAS\UI\Component\Input\Field\MultiSelect;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
abstract class AbstractConfigForm implements ConfigForm
{
    /**
     * @var \ilSrMsAbstractGUI
     */
    private $target_gui;
    /**
     * @var string
     */
    private $target_command;
    /**
     * @var Container
     */
    protected $container;
    /**
     * @var \srag\Plugins\SrMemberships\Translator
     */
    protected $translator;
    /**
     * @var \ilCtrl
     */
    protected $ctrl;
    /**
     * @var \ILIAS\Refinery\Factory
     */
    protected $refinery;
    /**
     * @var Factory
     */
    protected $ui_factory;
    /**
     * @var Config
     */
    protected $config;

    public function __construct(
        \ilSrMsAbstractGUI $target_gui,
        string $target_command,
        Config $config,
        Container $container
    ) {
        $this->container = $container;
        $this->target_gui = $target_gui;
        $this->target_command = $target_command;
        $this->config = $config;
        $this->translator = $container->translator();
        $this->ui_factory = $container->dic()->ui()->factory();
        $this->ctrl = $container->dic()->ctrl();
        $this->refinery = $container->dic()->refinery();
    }

    protected function getTextInput(
        string $config_key,
        string $label,
        string $byline = null
    ) : Text {
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
    ) : Checkbox {
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
    ) : Select {
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
    ) : \ILIAS\UI\Component\Input\Field\Group {
        $value = $this->config->get($config_key, null);
        if (in_array($all_value, $value ?? [], true)) {
            $group_value = self::GROUP_KEY_ALL;
        } else {
            $group_value = self::GROUP_KEY_SELECT;
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

        return $factory
            ->switchableGroup($group_fields, $label)
            ->withValue($group_value)
            ->withAdditionalTransformation(
                $this->refinery->custom()->transformation(function ($value) use ($config_key, $all_value) {
                    if ($value[0] === AbstractConfigForm::GROUP_KEY_ALL) {
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
    ) : MultiSelect {
        return $this->ui_factory->input()->field()->multiSelect($label, $options, $byline)
                                ->withValue($this->config->get($config_key, null))
                                ->withAdditionalTransformation(
                                    $this->getTransformation($config_key)
                                );
    }

    protected function getTransformation(string $config_key) : Transformation
    {
        return $this->refinery->custom()->transformation(function ($value) use ($config_key) {
            $this->config->set($config_key, $value);
            return $value;
        });
    }

    abstract protected function getFields() : array;

    public function getForm(
        ?ServerRequestInterface $with_request = null
    ) : \ILIAS\UI\Component\Input\Container\Form\Standard {
        $post_url = $this->ctrl->getLinkTarget($this->target_gui, $this->target_command);

        $standard = $this->ui_factory->input()->container()->form()->standard(
            $post_url,
            $this->getFields()
        );
        if ($with_request !== null) {
            $standard = $standard->withRequest($with_request);
        }

        return $standard;
    }
}
