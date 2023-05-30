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

use Psr\Http\Message\ServerRequestInterface;
use ILIAS\Refinery\Factory as Refinery;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Renderer;
use ILIAS\UI\Factory;
use srag\Plugins\SrMemberships\Translator;
use srag\Plugins\SrMemberships\Config\Configs;
use srag\Plugins\SrMemberships\Container;

/**
 * This is an abstraction for ILIAS command-class implementations.
 *
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 *
 * The gui-class wraps common dependencies, so that derived classes can
 * slim down their constructor.
 *
 * To enforce the usage of UI components, when rendering content in a
 * derived class, only the method @see ilSrMsAbstractGUI::render() should
 * be used.
 *
 * A notable structural point is, that all derived classes must also implement
 * an index method @see ilSrAbstractGUI::index().
 * The benefit of having an index method is, that redirects to a GUI class
 * can always be made the same, pointing to @see ilSrAbstractGUI::CMD_INDEX.
 *
 * @noinspection AutoloadingIssuesInspection
 */
abstract class ilSrMsAbstractGUI
{
    /**
     * @var string the method name each derived class must implement.
     */
    public const CMD_INDEX = 'index';

    /**
     * common language variables.
     */
    protected const MSG_PERMISSION_DENIED = 'msg_permission_denied';

    /**
     * @var ilSrMsAccessHandler
     */
    private $access_handler;
    /**
     * @var ilSrMsTabManager
     */
    private $tab_manager;
    /**
     * @var \srag\Plugins\SrMemberships\Workflow\WorkflowContainerRepository
     */
    protected $workflows;
    /**
     * @var Container
     */
    protected $container;
    /**
     * @var Configs
     */
    protected $config;
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var int|null
     */
    protected $object_ref_id;

    /**
     * @var int
     */
    protected $origin;

    // ILIAS dependencies:

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var ilDBInterface
     */
    protected $database;

    /**
     * @var Factory
     */
    protected $ui_factory;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var Refinery
     */
    protected $refinery;

    /**
     * @var ilCtrl
     */
    protected $ctrl;

    /**
     * @var ilObjUser
     */
    protected $user;

    /**
     * This instance variable is private to enforce the usage of UI components
     * through @see ilSrAbstractGUI::render().
     *
     * @var ilGlobalTemplateInterface
     */
    private $global_template;

    /**
     * Initializes common dependencies which are used in every derived GUI class.
     *
     * @throws LogicException if required dependencies are missing.
     */
    public function __construct()
    {
        global $DIC;

        $this->origin =
        $this->global_template = $DIC->ui()->mainTemplate();
        $this->ui_factory = $DIC->ui()->factory();
        $this->renderer = $DIC->ui()->renderer();
        $this->refinery = $DIC->refinery();
        $this->request = $DIC->http()->request();
        $this->ctrl = $DIC->ctrl();
        $this->user = $DIC->user();
        $this->database = $DIC->database();

        $this->container = Container::getInstance();
        $this->access_handler = $this->container->accessHandler();
        $this->translator = $this->container->translator();
        $this->tab_manager = $this->container->tabManager();
        $this->config = $this->container->config();

        $this->workflows = $this->container->workflows();
//        $this->keepNecessaryParametersAlive();
    }

    /**
     * This method dispatches ilCtrl's current command.
     *
     * Derived classes of this GUI are expected to be the last command-
     * class in the control flow, and must therefore dispatch ilCtrl's
     * command.
     */
    public function executeCommand(): void
    {
        $command = $this->ctrl->getCmd(self::CMD_INDEX);
        if (!method_exists(static::class, $command)) {
            throw new LogicException(static::class . " does not implement method '$command'.");
        }

        $this->setupGlobalTemplate(
            $this->global_template,
            $this->tab_manager
        );

        if (!$this->canUserExecute($this->access_handler, $command)) {
            $this->displayErrorMessage(self::MSG_PERMISSION_DENIED);
        } else {
            $this->{$command}();
        }

        // if base_class is ilUIPluginRouterGUI, we need to render the template
        if (strtolower($this->container->dic()->http()->request()->getQueryParams()['baseClass'] ?? '') === strtolower(
            ilUIPluginRouterGUI::class
        )) {
            $this->container->dic()->ui()->mainTemplate()->printToStdOut();
        }
    }

    /**
     * This method MUST set up the current page (global template).
     * It should manage things like the page-title, -description or tabs.
     */
    abstract protected function setupGlobalTemplate(ilGlobalTemplateInterface $template, ilSrMsTabManager $tabs): void;

    /**
     * This method MUST check if the given user can execute the command.
     * Note that all actions should be accessible for administrators.
     *
     * The command is passed as an argument in case the permissions
     * differ between the derived classes commands.
     *
     */
    abstract protected function canUserExecute(ilSrMsAccessHandler $access_handler, string $command): bool;

    /**
     * This method is the entry point of the command class.
     *
     * Redirects are (almost) always made to this method, when
     * coming from another GUI class.
     *
     * @see ilSrAbstractGUI::cancel() can also be used within
     * the same GUI class.
     */
    abstract protected function index(): void;

    /**
     * Redirects back to the derived classes index method.
     *
     * @see ilSrAbstractGUI::index()
     */
    protected function cancel(): void
    {
        $this->ctrl->redirectByClass(
            static::class,
            self::CMD_INDEX
        );
    }

    /**
     * Returns the value for a given parameter-name from the requests
     * current GET parameters.
     *
     * @param string $parameter
     * @return string|null
     */
    protected function getRequestParameter(string $parameter): ?string
    {
        return $this->request->getQueryParams()[$parameter] ?? null;
    }

    /**
     * Renders a given UI component to the current page (global template).
     *
     * @param Component $component
     */
    protected function render(Component $component): void
    {
        $this->global_template->setContent(
            $this->renderer->render($component)
        );
    }

    /**
     * Helper function that redirects to the given object (ref-id).
     *
     * @param int $ref_id
     */
    protected function redirectToRefId(int $ref_id): void
    {
        $this->ctrl->redirectToURL($this->container->objectInfoProvider()->getMembersTabLink($ref_id));
    }

    /**
     * Returns a form-action for the given command of the derived class.
     *
     * If a query-parameter is provided, the method checks if a value has been
     * submitted ($_GET) and if so, the parameter will be appended or used for
     * the form-action.
     *
     * @param string      $command
     * @param string|null $query_parameter
     * @return string
     */
    protected function getFormAction(string $command, string $query_parameter = null): string
    {
        // temporarily safe the parameter value if it has been requested.
        if (null !== $query_parameter &&
            null !== ($query_value = $this->getRequestParameter($query_parameter))
        ) {
            $this->ctrl->setParameterByClass(static::class, $query_parameter, $query_value);
        }

        // build the form action while the query value (maybe) is set.
        $form_action = $this->ctrl->getFormActionByClass(
            static::class,
            $command
        );

        // remove the parameter again once the form action has been generated.
        if (null !== $query_parameter) {
            $this->ctrl->clearParameterByClass(static::class, $query_parameter);
        }

        return $form_action;
    }

    /**
     * displays an error message for given lang-var on the next page (redirect).
     *
     * @param string $lang_var
     */
    protected function sendErrorMessage(string $lang_var): void
    {
        ilUtil::sendFailure($this->translator->txt($lang_var), true);
    }

    /**
     * displays an error message for given lang-var on the current page.
     *
     * @param string $lang_var
     */
    protected function displayErrorMessage(string $lang_var): void
    {
        $this->displayMessageToast($lang_var, 'failure');
    }

    /**
     * displays a success message for given lang-var on the next page (redirect).
     *
     * @param string $lang_var
     */
    protected function sendSuccessMessage(string $lang_var): void
    {
        $text = $this->translator->txt($lang_var);
        if (method_exists(ilUtil::class, 'sendSuccess')) {
            ilUtil::sendSuccess($text, true);
        } else {
            $this->container->dic()->ui()->mainTemplate()->setOnScreenMessage(
                'success',
                $text,
                true
            );
        }
    }

    /**
     * displays an success message for given lang-var on the current page.
     *
     * @param string $lang_var
     */
    protected function displaySuccessMessage(string $lang_var): void
    {
        $this->displayMessageToast($lang_var, 'success');
    }

    /**
     * displays an info message for given lang-var on the next page (redirect).
     *
     * @param string $lang_var
     */
    protected function sendInfoMessage(string $lang_var): void
    {
        ilUtil::sendInfo($this->translator->txt($lang_var), true);
    }

    /**
     * displays an info message for given lang-var on the current page.
     *
     * @param string $lang_var
     */
    protected function displayInfoMessage(string $lang_var): void
    {
        $this->displayMessageToast($lang_var, 'info');
    }

    /**
     * displays a message-toast for given lang-var and type on the current page.
     *
     * @param string $lang_var
     * @param string $type (info|success|failure)
     */
    private function displayMessageToast(string $lang_var, string $type): void
    {
        $this->render(
            $this->ui_factory->messageBox()->{$type}(
                $this->translator->txt($lang_var)
            )
        );
    }
}
