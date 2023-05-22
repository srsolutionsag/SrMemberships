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

namespace srag\Plugins\SrMemberships;

use srag\Plugins\SrMemberships\Implementations\PersonResolverFactory;
use srag\Plugins\SrMemberships\Config\Configs;
use srag\Plugins\SrMemberships\Provider\Tool\InternalToolProviderFactory;
use srag\Plugins\SrMemberships\Provider\Context\ObjectInfoProvider;
use srag\Plugins\SrMemberships\Provider\Context\UserAccessInfoProvider;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainerRepository;
use srag\Plugins\SrMemberships\Provider\Context\ContextFactory;
use srag\Plugins\SrMemberships\Workflow\Mode\ObjectModeRepository;
use srag\Plugins\SrMemberships\Workflow\Mode\ObjectModeDBRepository;
use srag\Plugins\SrMemberships\Workflow\ToolObjectConfig\ToolObjectConfigRepository;
use srag\Plugins\SrMemberships\Workflow\ToolObjectConfig\ToolObjectConfigDBRepository;
use srag\Plugins\SrMemberships\Person\Persons\PersonListGenerators;
use srag\Plugins\SrMemberships\Person\Account\AccountListGenerators;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class Container extends \Pimple\Container
{
    public static function getInstance(?\ilSrMembershipsPlugin $plugin = null): Container
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new Container($plugin);
        }

        return $instance;
    }

    private function __construct(?\ilSrMembershipsPlugin $plugin = null)
    {
        global $DIC;

        $container['plugin'] = function () use ($plugin): ?\ilSrMembershipsPlugin {
            return $plugin;
        };

        $container['access_handler'] = function () use ($DIC): \ilSrMsAccessHandler {
            return new \ilSrMsAccessHandler($DIC->rbac(), $DIC->user());
        };

        $container['config'] = function () use ($DIC): Configs {
            return new Configs($DIC->database());
        };

        $container['translator'] = function () use ($DIC): Translator {
            return new \ilSrMsTranslator();
        };

        $container['origin'] = function () use ($DIC): int {
            return \ilSrMembershipsDispatcher::getOriginType();
        };

        $container['tab_manager'] = function (Container $c) use ($DIC): \ilSrMsTabManager {
            return new \ilSrMsTabManager(
                $c
            );
        };

        $container['dic'] = function () use ($DIC): \ILIAS\DI\Container {
            return $DIC;
        };

        $container['workflow_repository'] = function (Container $c): WorkflowContainerRepository {
            return new WorkflowContainerRepository($c);
        };

        $container['context_factory'] = function (Container $c): ContextFactory {
            return new ContextFactory($c);
        };

        $container['object_mode_repository'] = function (Container $c): ObjectModeRepository {
            return new ObjectModeDBRepository($c->dic()->database());
        };

        $container['tool_object_config_repository'] = function (Container $c): ToolObjectConfigRepository {
            return new ToolObjectConfigDBRepository($c->dic()->database());
        };

        $container['person_list_generators'] = function (Container $c): PersonListGenerators {
            return new PersonListGenerators($c);
        };

        $container['account_list_generators'] = function (Container $c): AccountListGenerators {
            return new AccountListGenerators($c);
        };

        $container['object_info_provider'] = function (Container $c): ObjectInfoProvider {
            return new ObjectInfoProvider(
                $c->dic()->repositoryTree(),
                $c->dic()->ctrl(),
                $c->dic()->http()->request(),
                $this->dic()->rbac()->review()
            );
        };

        $container['user_access_info_provider'] = function (Container $c): UserAccessInfoProvider {
            return new UserAccessInfoProvider(
                $c->dic()->rbac()->system(),
                $c->dic()->rbac()->review(),
                $c->objectInfoProvider()
            );
        };

        parent::__construct($container);
    }

    public function objectInfoProvider(): ObjectInfoProvider
    {
        return $this['object_info_provider'];
    }

    public function userAccessInfoProvider(): UserAccessInfoProvider
    {
        return $this['user_access_info_provider'];
    }

    public function plugin(): ?\ilSrMembershipsPlugin
    {
        return $this['plugin'];
    }

    public function dic(): \ILIAS\DI\Container
    {
        return $this['dic'];
    }

    public function tabManager(): \ilSrMsTabManager
    {
        return $this['tab_manager'];
    }

    public function origin(): int
    {
        return $this['origin'];
    }

    public function config(): Configs
    {
        return $this['config'];
    }

    public function translator(): Translator
    {
        return $this['translator'];
    }

    public function accessHandler(): \ilSrMsAccessHandler
    {
        return $this['access_handler'];
    }

    public function workflows(): WorkflowContainerRepository
    {
        return $this['workflow_repository'];
    }

    public function contextFactory(): ContextFactory
    {
        return $this['context_factory'];
    }

    public function objectModeRepository(): ObjectModeRepository
    {
        return $this['object_mode_repository'];
    }

    public function toolObjectConfigRepository(): ToolObjectConfigRepository
    {
        return $this['tool_object_config_repository'];
    }

    public function personListGenerators(): PersonListGenerators
    {
        return $this['person_list_generators'];
    }

    public function accountListGenerators(): AccountListGenerators
    {
        return $this['account_list_generators'];
    }
}
