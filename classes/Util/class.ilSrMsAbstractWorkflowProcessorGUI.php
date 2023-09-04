<?php /**
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
    /**
     * @var WorkflowFormBuilder
     */
    protected $form_builder;

    public function __construct()
    {
        parent::__construct();
        $this->form_builder = new WorkflowFormBuilder($this->container);
    }

    protected function setupGlobalTemplate(ilGlobalTemplateInterface $template, ilSrMsTabManager $tabs) : void
    {
        $fallback_ref_id = $this->getRequestParameter(self::FALLBACK_REF_ID) ?? null;
        $tabs->addBackToObjectMembersTab((int) $fallback_ref_id);
    }

    protected function index() : void
    {
        $workflow_container_id = $this->getRequestParameter(self::WORKFLOW_CONTAINER_ID) ?? null;
        $fallback_ref_id = $this->getRequestParameter(self::FALLBACK_REF_ID) ?? null;
        if ($workflow_container_id === null || $fallback_ref_id === null) {
            throw new ilException('No workflow container id or fallback ref id given');
        }

        $workflow_container = $this->container->workflows()->getWorkflowById($workflow_container_id);
        $context = $this->buildContext($fallback_ref_id);
        $this->handleWorkflow($workflow_container, $this->request, $context);
    }

    abstract protected function handleWorkflow(
        WorkflowContainer $workflow_container,
        ServerRequestInterface $request,
        Context $context
    ) : void;

    protected function handleDelete() : void
    {
        $workflow_container_id = $this->getRequestParameter(self::WORKFLOW_CONTAINER_ID) ?? null;
        $fallback_ref_id = $this->getRequestParameter(self::FALLBACK_REF_ID) ?? null;
        $context = $this->buildContext($fallback_ref_id);
        $workflow_container = $this->container->workflows()->getWorkflowById($workflow_container_id);

        $this->container->toolObjectConfigRepository()->clear($context->getCurrentRefId(), $workflow_container);

        $this->sendSuccessMessage($this->translator->txt('msg_object_config_stored'));
        $this->redirectToRefId($context->getCurrentRefId());
    }

    protected function buildContext(?string $fallback_ref_id) : Context
    {
        return $this->container->contextFactory()->get((int) $fallback_ref_id, $this->user->getId());
    }
}
