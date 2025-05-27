<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Provider\Tool;

use srag\Plugins\SrMemberships\Workflow\WorkflowContainerRepository;
use srag\Plugins\SrMemberships\Provider\Context\ContextFactory;
use srag\Plugins\SrMemberships\Container\Container;
use ILIAS\GlobalScreen\ScreenContext\Stack\ContextCollection;
use ILIAS\GlobalScreen\ScreenContext\Stack\CalledContexts;
use ILIAS\GlobalScreen\Scope\Tool\Provider\AbstractDynamicToolPluginProvider;
use ILIAS\GlobalScreen\Identification\IdentificationProviderInterface;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class CollectedMainBarProvider extends AbstractDynamicToolPluginProvider
{
    protected IdentificationProviderInterface $if;
    private ?WorkflowContainerRepository $workflow_repository = null;
    private ?ContextFactory $context_factory = null;

    public function getToolsForContextStack(CalledContexts $called_contexts): array
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

    public function isInterestedInContexts(): ContextCollection
    {
        return $this->context_collection->repository();
    }

    public function init(Container $container): void
    {
        $this->workflow_repository = $container->workflows();
        $this->context_factory = $container->contextFactory();
    }
}
