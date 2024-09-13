<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use srag\Plugins\SrMemberships\Action\Summary;
use srag\Plugins\SrMemberships\Person\Persons\PersonList;
use ILIAS\UI\Component\Input\Container\Form\Standard;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use Psr\Http\Message\ServerRequestInterface;
use srag\Plugins\SrMemberships\Provider\Context\Context;
use srag\Plugins\SrMemberships\Workflow\Mode\Sync\SyncModes;
use srag\Plugins\SrMemberships\Workflow\Mode\Run\NullRunModes;
use srag\Plugins\SrMemberships\Workflow\Config\WorkflowConfig;
use ILIAS\UI\Component\Input\Field\Select;
use srag\Plugins\SrMemberships\AccountCreator;

/**
 * Class ilSrMsStoreObjectConfigGUI
 *
 * @ilCtrl_isCalledBy ilSrMsStoreObjectConfigGUI: ilUIPluginRouterGUI
 */
class ilSrMsStoreObjectConfigGUI extends ilSrMsAbstractWorkflowProcessorGUI
{
    protected function setupGlobalTemplate(ilGlobalTemplateInterface $template, ilSrMsTabManager $tabs): void
    {
        parent::setupGlobalTemplate($template, $tabs);
        $template->setTitle($this->translator->txt('store_object_config'));
    }

    protected function canUserExecute(ilSrMsAccessHandler $access_handler, string $command): bool
    {
        return true;
    }

    protected function handleWorkflow(
        WorkflowContainer $workflow_container,
        ServerRequestInterface $request,
        Context $context
    ): void {
        $form = $this->form_builder->getForm($context, $workflow_container)->withRequest($request);
        $data = $form->getData();
        if ($data !== null) {
            $summary = $this->performActions($context, $workflow_container);

            $this->sendSuccessMessage($this->translator->txt('msg_object_config_stored'));

            // user creation allowed
            $user_creation = $this->container->config()->byMatriculation()->get(
                WorkflowConfig::F_USER_CREATION,
                -1
            );

            $user_creation_allowed = $user_creation !== -1;

            // create missing accounts first
            if ($user_creation_allowed && $summary->getPersonsNotFound()->count() > 0) {
                // redirect to user creation screen
                $this->ctrl->redirect($this, 'userSelect');
                return;
            }

            $this->redirectToRefId($context->getCurrentRefId());
        }
        $this->render($form);
    }

    protected function selectUsersForCreation(
        WorkflowContainer $workflow_container,
        ServerRequestInterface $request,
        Context $context
    ): void {
        // persons to create
        $person_list = $workflow_container->getActionHandler($context)->getNotFoundPersonsList(
            $workflow_container,
            $context
        );
        $form = $this->buildUserCreationForm($person_list);

        // first 3 items
        $i = 0;
        $pre_selection_values = [];
        foreach ($person_list->getPersons() as $person) {
            if ($i > 2) {
                break;
            }
            $pre_selection_values[1][] = $person->getUniqueIdentification();
            foreach ($person->getAdditionalAttributes() as $k => $additional_attribute) {
                $pre_selection_values[$k + 2][] = $additional_attribute;
            }
            $i++;
        }
        $pre_selection_values = array_map(static fn ($v): string => implode(', ', $v), $pre_selection_values);

        $pre_selection_inputs = [];
        foreach ($form->getInputs() as $section) {
            $inputs = $section->getInputs();
            foreach ($inputs as $index => $input) {
                if (!$input instanceof Select) {
                    continue;
                }
                // build preselection inputs
                $pre_selection_inputs[] = $this->ui_factory->input()->field()->select(
                    $input->getLabel(),
                    $pre_selection_values
                )->withAdditionalOnLoadCode(fn ($id): string => "let select = document.getElementById('$id');
                                select.addEventListener('change', function() {
                                    let selected_value = select.value;
                                    let event = new CustomEvent('changedDefault', {
                                            bubbles: true,
                                            cancelable: false,
                                            detail: {index: '$index', value: selected_value-1}
                                    });
                                    document.dispatchEvent(event);
                                });")->withValue(null);
            }
            break;
        }

        $info = $this->ui_factory->messageBox()->info($this->translator->txt('user_creation_info'));

        $prefill_panel = $this->ui_factory->panel()->secondary()->legacy(
            $this->translator->txt('user_creation_prefill'),
            $this->ui_factory->legacy($this->renderer->render($pre_selection_inputs))
        );

        $this->render(
            [
                $info,
                $prefill_panel,
                $form
            ]
        );
    }

    protected function performUserCreate(
        WorkflowContainer $workflow_container,
        ServerRequestInterface $request,
        Context $context
    ): void {
        // persons to create
        $person_list = $workflow_container->getActionHandler($context)->getNotFoundPersonsList(
            $workflow_container,
            $context
        );

        $form = $this->buildUserCreationForm($person_list)->withRequest($request);
        $value = $form->getData();
        if ($value === null) {
            $this->render(
                $form
            );
            return;
        }

        $global_roles = (array) $this->container->config()->byMatriculation()->get(
            WorkflowConfig::F_USER_CREATION,
            -1
        );

        $account_creator = new AccountCreator(
            $workflow_container,
            $context,
            $value,
            false,
            $global_roles
        );

        $account_creator->perform();
    }

    protected function buildUserCreationForm(PersonList $person_list): Standard
    {
        $inputs = [];

        $event_listener = fn (string $index): Closure => fn ($id): string => "document.addEventListener('changedDefault', function(event) {
                        // check for index 
                        if (event.detail.index !== '$index') {
                            return;
                        }
                        let select = document.getElementById('$id');
                        select.value = event.detail.value;
                    });";

        foreach ($person_list->getPersons() as $person) {
            $additional_attributes = array_merge(
                [$person->getUniqueIdentification()],
                $person->getAdditionalAttributes()
            );
            $id_to_value = $this->refinery->custom()->transformation(fn ($v) => $additional_attributes[$v]);
            $count = count($additional_attributes);
            $next = static function () use ($count): ?int {
                static $i = 0;
                $return = $i % $count;
                $i++;
                return $return;
            };

            $fields = $this->ui_factory->input()->field();

            $hidden = $fields->hidden()->withValue($person->getUniqueIdentification());

            $checkbox = $fields->checkbox($this->translator->txt('user_creation_create'))
                               ->withValue(true);

            $login = $fields->select($this->translator->txt('user_creation_login'), $additional_attributes)
                            ->withRequired(true)
                            ->withValue($next())
                            ->withAdditionalTransformation($id_to_value)
                            ->withAdditionalOnLoadCode($event_listener('login'));

            $email = $fields->select($this->translator->txt('user_creation_email'), $additional_attributes)
                            ->withRequired(true)
                            ->withValue($next())
                            ->withAdditionalTransformation($id_to_value)
                            ->withAdditionalOnLoadCode($event_listener('email'));

            $firstname = $fields->select($this->translator->txt('user_creation_firstname'), $additional_attributes)
                            ->withRequired(true)
                            ->withValue($next())
                            ->withAdditionalTransformation($id_to_value)
                            ->withAdditionalOnLoadCode($event_listener('firstname'));

            $lastname = $fields->select($this->translator->txt('user_creation_lastname'), $additional_attributes)
                            ->withRequired(true)
                            ->withValue($next())
                            ->withAdditionalTransformation($id_to_value)
                            ->withAdditionalOnLoadCode($event_listener('lastname'));

            $password = $fields->select($this->translator->txt('user_creation_password'), $additional_attributes)
                               ->withRequired(false)
                               ->withValue($next())
                               ->withAdditionalTransformation($id_to_value)
                               ->withAdditionalOnLoadCode($event_listener('password'));

            $ext_account = $fields->select($this->translator->txt('user_creation_ext_account'), $additional_attributes)
                               ->withRequired(false)
                               ->withValue($next())
                               ->withAdditionalTransformation($id_to_value)
                               ->withAdditionalOnLoadCode($event_listener('ext_account'));


            $section = $fields->section([
                'primary' => $hidden,
                'create' => $checkbox,
                'login' => $login,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'password' => $password,
                'ext_account' => $ext_account
            ], $person->getUniqueIdentification());

            $inputs[] = $section;
        }

        // build creation form
        $form = $this->ui_factory->input()->container()->form()->standard(
            $this->ctrl->getLinkTarget($this, 'userCreate'),
            $inputs
        );
        return $form;
    }

    protected function performActions(
        Context $context,
        WorkflowContainer $workflow_container
    ): Summary {
        // Run Action Handler
        $sync_mode = $this->container->objectModeRepository()->getSyncMode(
            $context->getCurrentRefId(),
            $workflow_container
        );
        $sync_modes = new SyncModes($sync_mode);

        $run_modes = $this->container->objectModeRepository()->getRunModes(
            $context->getCurrentRefId(),
            $workflow_container
        ) ?? new NullRunModes();

        $summary = $workflow_container->getActionHandler($context)->performActions(
            $workflow_container,
            $context,
            $sync_modes,
            $run_modes
        );

        switch (true) {
            case $summary->isNull():
                break;
            case $summary->isOK():
                $this->sendInfoMessage(nl2br($summary->getSummary()));
                break;
            case $summary->isNOK():
                $this->sendErrorMessage(nl2br($summary->getSummary()));
                break;
        }
        return $summary;
    }

}
