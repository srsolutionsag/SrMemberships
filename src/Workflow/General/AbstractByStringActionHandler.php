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
use srag\Plugins\SrMemberships\Workflow\Mode\Modes;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Action\BaseActionHandler;
use srag\Plugins\SrMemberships\Action\Summary;
use srag\Plugins\SrMemberships\Container\Container;
use ILIAS\ResourceStorage\Services;
use InvalidArgumentException;
use srag\Plugins\SrMemberships\Person\Persons\PersonList;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
abstract class AbstractByStringActionHandler extends BaseActionHandler
{
    /**
     * @var Services
     */
    protected $irss;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->irss = $container->dic()->resourceStorage();
    }

    abstract protected function getPersonList(string $text) : PersonList;

    public function performActions(
        WorkflowContainer $workflow_container,
        Context $context,
        Modes $modes
    ) : Summary {
        if ($context->isCli() && !$modes->isModeSet(Modes::RUN_AS_CRONJOB)) {
            return Summary::empty();
        }
        if (!$context->isCli() && !$modes->isModeSet(Modes::RUN_ON_SAVE)) {
            return Summary::empty();
        }

        $object_config = $this->container->toolObjectConfigRepository()->get(
            $context->getCurrentRefId(),
            $workflow_container
        );

        $type = $object_config['type'] ?? null;
        switch ($type) {
            case 'text':
                $strings = $object_config['content']['text_list'] ?? '';
                break;
            case 'file':
                $rid = $object_config['content']['file_list'][0] ?? '';
                $rid = $this->irss->manage()->find($rid);
                if (!$rid) {
                    return Summary::error('File not found');
                }
                $strings = (string) $this->irss->consume()->stream($rid)->getStream();
                break;
            default:
                $strings = '';
        }

        try {
            $person_list = $this->getPersonList($strings);
        } catch (InvalidArgumentException $e) {
            return Summary::throwable($e);
        }

        $account_list = $this->persons_to_accounts->translate($person_list);

        return $this->generalHandling($context, $account_list, $modes);
    }
}
