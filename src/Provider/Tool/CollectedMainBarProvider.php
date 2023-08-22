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

namespace srag\Plugins\SrMemberships\Provider\Tool;

use ILIAS\GlobalScreen\ScreenContext\Stack\ContextCollection;
use ILIAS\GlobalScreen\ScreenContext\Stack\CalledContexts;
use ILIAS\GlobalScreen\Scope\Tool\Provider\AbstractDynamicToolPluginProvider;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class CollectedMainBarProvider extends AbstractDynamicToolPluginProvider
{

    /**
     * @var \srag\Plugins\SrMemberships\Translator|null
     */
    private $translator = null;
    /**
     * @var \srag\Plugins\SrMemberships\Config\Configs|null
     */
    private $config = null;
    /**
     * @var \srag\Plugins\SrMemberships\Provider\Context\ObjectInfoProvider|null
     */
    private $object_info_resolver = null;
    /**
     * @var \srag\Plugins\SrMemberships\Provider\Context\UserAccessInfoProvider|null
     */
    private $access_info_resolver = null;
    /**
     * @var \srag\Plugins\SrMemberships\Workflow\WorkflowContainerRepository|null
     */
    private $workflow_repository = null;
    /**
     * @var \srag\Plugins\SrMemberships\Provider\Context\ContextFactory|null
     */
    private $context_factory = null;

    public function getToolsForContextStack(CalledContexts $called_contexts) : array
    {
        $current_ref_id = $called_contexts->current()->hasReferenceId()
            ? $called_contexts->current()->getReferenceId()->toInt()
            : null;
        if ($current_ref_id === null) {
            return [];
        }

        $context = $this->context_factory->get(
            $current_ref_id,
            $this->dic->user()->getId()
        );

        $tools = [];

        foreach ($this->workflow_repository->getEnabledWorkflows() as $workflow_container) {
            $tool = $workflow_container->getToolProvider()->getTool($context, $this->factory, $this->if);
            if ($tool !== null) {
                $tools[] = $tool;
            }
        }

        return $tools;
    }

    public function isInterestedInContexts() : ContextCollection
    {
        return $this->context_collection->repository();
    }

    public function init(\srag\Plugins\SrMemberships\Container\Container $container)
    {
        $this->translator = $container->translator();
        $this->config = $container->config();
        $this->object_info_resolver = $container->objectInfoProvider();
        $this->access_info_resolver = $container->userAccessInfoProvider();
        $this->workflow_repository = $container->workflows();
        $this->context_factory = $container->contextFactory();
    }
}
