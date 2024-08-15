<?php
/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);
use srag\Plugins\SrMemberships\Action\Summary;

use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use Psr\Http\Message\ServerRequestInterface;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Workflow\WorkflowFormBuilder;

abstract class ilSrMsAbstractWorkflowProcessorGUI extends ilSrMsAbstractGUI
{
    public const CMD_HANDLE_WORKFLOW = 'handleWorkflow';
    public const CMD_HANDLE_WORKFLOW_DELETION = 'handleDelete';
    public const WORKFLOW_CONTAINER_ID = 'wfcid';
    public const FALLBACK_REF_ID = 'fallback_ref_id';
    protected WorkflowFormBuilder $form_builder;

    public function __construct()
    {
        parent::__construct();
        $this->form_builder = new WorkflowFormBuilder($this->container);
    }

    protected function setupGlobalTemplate(ilGlobalTemplateInterface $template, ilSrMsTabManager $tabs): void
    {
        $fallback_ref_id = $this->getRequestParameter(self::FALLBACK_REF_ID);
        $tabs->addBackToObjectMembersTab((int) $fallback_ref_id);
    }

    protected function index(): void
    {
        [$workflow_container, $context] = $this->prepareContext();
        $this->handleWorkflow($workflow_container, $this->request, $context);
    }

    abstract protected function handleWorkflow(
        WorkflowContainer $workflow_container,
        ServerRequestInterface $request,
        Context $context
    ): void;

    protected function userSelect(): void
    {
        [$workflow_container, $context] = $this->prepareContext();
        $this->selectUsersForCreation($workflow_container, $this->request, $context);
    }

    protected function userCreate(): void
    {
        [$workflow_container, $context] = $this->prepareContext();
        $this->performUserCreate($workflow_container, $this->request, $context);
        $this->performActions($context, $workflow_container);
        $this->redirectToRefId($context->getCurrentRefId());
    }

    abstract protected function selectUsersForCreation(
        WorkflowContainer $workflow_container,
        ServerRequestInterface $request,
        Context $context
    ): void;

    abstract protected function performUserCreate(
        WorkflowContainer $workflow_container,
        ServerRequestInterface $request,
        Context $context
    ): void;

    abstract protected function performActions(
        Context $context,
        WorkflowContainer $workflow_container
    ): Summary;

    protected function handleDelete(): void
    {
        [$workflow_container, $context] = $this->prepareContext();

        $this->container->toolObjectConfigRepository()->clear($context->getCurrentRefId(), $workflow_container);

        $this->sendSuccessMessage($this->translator->txt('msg_object_config_stored'));
        $this->redirectToRefId($context->getCurrentRefId());
    }

    protected function buildContext(?string $fallback_ref_id): Context
    {
        return $this->container->contextFactory()->get((int) $fallback_ref_id, $this->user->getId());
    }

    /**
     * @return array<WorkflowContainer, Context>
     */
    protected function prepareContext(): array
    {
        $workflow_container_id = $this->getRequestParameter(self::WORKFLOW_CONTAINER_ID);
        $fallback_ref_id = $this->getRequestParameter(self::FALLBACK_REF_ID);
        if ($workflow_container_id === null || $fallback_ref_id === null) {
            throw new ilException('No workflow container id or fallback ref id given');
        }
        $this->ctrl->saveParameter($this, self::FALLBACK_REF_ID);
        $this->ctrl->saveParameter($this, self::WORKFLOW_CONTAINER_ID);

        $workflow_container = $this->container->workflows()->getWorkflowById($workflow_container_id);
        $context = $this->buildContext($fallback_ref_id);
        return [$workflow_container, $context];
    }
}
