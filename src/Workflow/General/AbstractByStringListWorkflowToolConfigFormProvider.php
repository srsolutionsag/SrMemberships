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

namespace srag\Plugins\SrMemberships\Workflow\General;

use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Workflow\ToolObjectConfig\ToolConfigFormProvider;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use ILIAS\UI\Component\Input\Field\Section;
use srag\Plugins\SrMemberships\TrafoGenerator;
use srag\Plugins\SrMemberships\Person\Persons\Source\StringPersonSource;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
abstract class AbstractByStringListWorkflowToolConfigFormProvider implements ToolConfigFormProvider
{
    use TrafoGenerator;

    public const F_TYPE = 'type';
    public const F_CONTENT = 'content';
    public const TYPE_TEXT = 'text';
    public const TYPE_FILE = 'file';
    public const F_TEXT_LIST = 'text_list';
    public const F_FILE_LIST = 'file_list';

    /**
     * @var WorkflowContainer
     */
    protected $workflow_container;
    /**
     * @var \srag\Plugins\SrMemberships\Container\Container
     */
    protected $container;
    /**
     * @var \ILIAS\UI\Factory
     */
    protected $ui_factory;
    /**
     * @var \srag\Plugins\SrMemberships\Translator
     */
    protected $translator;

    public function __construct(
        Container $container,
        WorkflowContainer $workflow_container
    ) {
        $this->container = $container;
        $this->translator = $this->container->translator();
        $this->workflow_container = $workflow_container;
        $this->ui_factory = $this->container->dic()->ui()->factory();
    }

    public function getFormSection(
        Context $context
    ) : Section {
        $factory = $this->ui_factory->input()->field();

        $object_config = $this->container->toolObjectConfigRepository()->get(
            $context->getCurrentRefId(),
            $this->workflow_container
        );

        $type = $object_config[self::F_TYPE] ?? self::TYPE_TEXT;
        $file_list = $object_config[self::F_CONTENT][self::F_FILE_LIST] ?? null;
        $text_list = $object_config[self::F_CONTENT][self::F_TEXT_LIST] ?? '';
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $factory->section(
            [
                $factory->switchableGroup(
                    [
                        self::TYPE_TEXT => $factory->group(
                            [
                                self::F_TEXT_LIST => $factory
                                    ->textarea(
                                        '',
                                        $this->translator->txt($this->getPrefix() . '_text_list_byline')
                                    )->withValue($text_list)
                            ],
                            $this->translator->txt($this->getPrefix() . '_text_list')
                        ),
                        self::TYPE_FILE => $factory
                            ->group(
                                [
                                    self::F_FILE_LIST => $factory
                                        ->file(
                                            new \ilSrMsGeneralUploadHandlerGUI(),
                                            '',
                                            $this->translator->txt($this->getPrefix() . '_file_list_byline')
                                        )
                                        ->withAcceptedMimeTypes(
                                            array_merge(
                                                [
                                                    StringPersonSource::MIME_TEXT_PLAIN,
                                                    StringPersonSource::MIME_TEXT_CSV
                                                ],
                                                StringPersonSource::MIME_EXCEL
                                            )
                                        )
                                        ->withValue($file_list)

                                ],
                                $this->translator->txt($this->getPrefix() . '_file_import')
                            ),
                    ],
                    $this->translator->txt($this->getPrefix() . '_source')
                )->withValue($type)
            ],
            $this->container->translator()->txt($this->getPrefix() . '_header')
        )->withAdditionalTransformation(
            $this->trafo(
                function ($v) {
                    return [
                        self::F_TYPE => $v[0][0],
                        self::F_CONTENT => $v[0][1]
                    ];
                }
            )
        );
    }

    abstract protected function getPrefix() : string;
}
