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

use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Action\BaseActionHandler;
use srag\Plugins\SrMemberships\Action\Summary;
use srag\Plugins\SrMemberships\Container\Container;
use ILIAS\ResourceStorage\Services;
use InvalidArgumentException;
use srag\Plugins\SrMemberships\Person\Persons\PersonList;
use srag\Plugins\SrMemberships\Workflow\Mode\Sync\SyncModes;
use srag\Plugins\SrMemberships\Workflow\Mode\Run\RunModes;
use srag\Plugins\SrMemberships\Person\Persons\Source\StringPersonSource;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
abstract class AbstractByStringActionHandler extends BaseActionHandler
{
    protected Services $irss;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->irss = $container->dic()->resourceStorage();
    }

    abstract public function getPersonList(string $text, ?string $original_mime_type = null): PersonList;

    public function performActions(
        WorkflowContainer $workflow_container,
        Context $context,
        SyncModes $sync_modes,
        RunModes $run_modes
    ): Summary {
        if (($summary = $this->checkRunMode(
            $context,
            $run_modes
        )) instanceof Summary) {
            return $summary;
        }

        $raw_data = $this->getRawData($workflow_container, $context);
        $strings = $raw_data['strings'];
        $mime_type = $raw_data['mime_type'];

        try {
            $person_list = $this->getPersonList($strings, $mime_type);
        } catch (InvalidArgumentException $e) {
            return Summary::throwable($e);
        }

        $account_list = $this->persons_to_accounts->translate($person_list, true);
        // $person_list now contains only the persons that could not be translated

        return $this->generalHandling(
            $context,
            $account_list,
            $person_list,
            $sync_modes
        );
    }

    public function getNotFoundPersonsList(
        WorkflowContainer $workflow_container,
        Context $context
    ): PersonList {
        $raw_data = $this->getRawData($workflow_container, $context);
        $strings = $raw_data['strings'];
        $mime_type = $raw_data['mime_type'];

        return $this->persons_to_accounts->filterFound($this->getPersonList($strings, $mime_type));
    }

    public function getRawData(
        WorkflowContainer $workflow_container,
        Context $context
    ): array {
        $object_config = $this->container->toolObjectConfigRepository()->get(
            $context->getCurrentRefId(),
            $workflow_container
        );

        $type = $object_config['type'] ?? null;
        $strings = '';
        switch ($type) {
            case 'text':
                $mime_type = null;
                $strings = $object_config['content']['text_list'] ?? '';
                break;
            case 'file':
                $rid = $object_config['content']['file_list'][0] ?? '';
                $rid = $this->irss->manage()->find($rid);
                if ($rid === null) {
                    return Summary::error($this->container->translator()->txt('msg_file_not_found'));
                }
                $strings = (string) $this->irss->consume()->stream($rid)->getStream();
                $mime_type = $this->irss->manage()->getCurrentRevision($rid)->getInformation()->getMimeType();
                if (in_array($mime_type, StringPersonSource::MIME_EXCEL)) {
                    $strings = $this->irss->consume()->stream($rid)->getStream()->getMetadata()['uri'];
                }
                break;
            default:
                $mime_type = null;
                $strings = '';
        }

        return [
            'strings' => $strings,
            'mime_type' => $mime_type
        ];
    }

}
