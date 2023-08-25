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

namespace srag\Plugins\SrMemberships\Workflow\ByLogin;

use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Workflow\ToolObjectConfig\ToolConfigFormProvider;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use ILIAS\UI\Component\Input\Field\Section;
use srag\Plugins\SrMemberships\TrafoGenerator;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ByLoginWorkflowToolConfigFormProvider implements ToolConfigFormProvider
{
    use TrafoGenerator;

    /**
     * @var WorkflowContainer
     */
    private $workflow_container;
    /**
     * @var \srag\Plugins\SrMemberships\Container\Container
     */
    private $container;
    /**
     * @var \ILIAS\UI\Factory
     */
    private $ui_factory;
    /**
     * @var \srag\Plugins\SrMemberships\Translator
     */
    private $translator;

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

        $type = $object_config['type'] ?? 'text';
        $file_list = $object_config['content']['file_list'] ?? null;
        $text_list = $object_config['content']['text_list'] ?? '';
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $factory->section(
            [
                $factory->switchableGroup(
                    [
                        'text' => $factory->group(
                            [
                                'text_list' => $factory
                                    ->textarea(
                                        ''
                                    )->withValue($text_list)
                            ],
                            $this->translator->txt('by_login_text_list')
                        ),
                        'file' => $factory
                            ->group(
                                [
                                    'file_list' => $factory
                                        ->file(
                                            new \ilSrMsGeneralUploadHandlerGUI(),
                                            ''
                                        )
                                        ->withAcceptedMimeTypes(['text/plain'])
                                        ->withValue($file_list)

                                ],
                                $this->translator->txt('by_login_file_import')
                            ),
                    ],
                    $this->translator->txt('by_login_source')
                )->withValue($type)
            ],
            $this->container->translator()->txt('by_login_header')
        )->withAdditionalTransformation(
            $this->trafo(
                function ($v) {
                    return [
                        'type' => $v[0][0],
                        'content' => $v[0][1]
                    ];
                }
            )
        );
    }
}
