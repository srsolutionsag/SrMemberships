<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\General;

use ILIAS\UI\Factory;
use srag\Plugins\SrMemberships\Translator;
use ilSrMsGeneralUploadHandlerGUI;
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
    protected Container $container;
    protected WorkflowContainer $workflow_container;

    public const F_TYPE = 'type';
    public const F_CONTENT = 'content';
    public const TYPE_TEXT = 'text';
    public const TYPE_FILE = 'file';
    public const F_TEXT_LIST = 'text_list';
    public const F_FILE_LIST = 'file_list';
    protected Factory $ui_factory;
    protected Translator $translator;

    public function __construct(
        Container $container,
        WorkflowContainer $workflow_container
    ) {
        $this->container = $container;
        $this->workflow_container = $workflow_container;
        $this->translator = $this->container->translator();
        $this->ui_factory = $this->container->dic()->ui()->factory();
    }

    public function getFormSection(
        Context $context
    ): Section {
        $factory = $this->ui_factory->input()->field();

        $object_config = $this->container->toolObjectConfigRepository()->get(
            $context->getCurrentRefId(),
            $this->workflow_container
        );

        $type = empty($object_config[self::F_TYPE]) ? self::TYPE_TEXT : $object_config[self::F_TYPE];
        $file_list = $object_config[self::F_CONTENT][self::F_FILE_LIST] ?? [];
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
                                            new ilSrMsGeneralUploadHandlerGUI(),
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
                fn ($v): array => [
                    self::F_TYPE => $v[0][0],
                    self::F_CONTENT => $v[0][1]
                ]
            )
        );
    }

    abstract protected function getPrefix(): string;
}
