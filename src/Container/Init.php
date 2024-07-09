<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Container;

use ilSrMembershipsPlugin;
use ilSrMsAccessHandler;
use ilSrMsTranslator;
use ilSrMembershipsDispatcherGUI;
use ilSrMsTabManager;
use srag\Plugins\SrMemberships\Config\Configs;
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
use srag\Plugins\SrMemberships\Translator;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
final class Init
{
    /**
     * @var Container
     */
    private static $container;

    public static function init(
        \ILIAS\DI\Container $ilias_container,
        ?ilSrMembershipsPlugin $plugin = null
    ): Container {
        if (isset(self::$container)) {
            return self::$container;
        }
        $container = new Container();

        if ($plugin !== null) {
            $container->glue(ilSrMembershipsPlugin::class, fn () => $plugin);
        }

        $container->glue(
            ilSrMsAccessHandler::class,
            fn (): ilSrMsAccessHandler => new ilSrMsAccessHandler($ilias_container->rbac(), $ilias_container->user())
        );

        $container->glue(Configs::class, fn (): Configs => new Configs($ilias_container->database()));

        $container->glue(Translator::class, fn (): Translator => new ilSrMsTranslator());

        $container['_origin'] = fn (): int => ilSrMembershipsDispatcherGUI::getOriginType();

        $container->glue(ilSrMsTabManager::class, fn (Container $c): ilSrMsTabManager => new ilSrMsTabManager(
            $c
        ));

        $container->glue(\ILIAS\DI\Container::class, fn (): \ILIAS\DI\Container => $ilias_container);

        $container->glue(
            WorkflowContainerRepository::class,
            fn (Container $c): WorkflowContainerRepository => new WorkflowContainerRepository($c)
        );

        $container->glue(ContextFactory::class, fn (Container $c): ContextFactory => new ContextFactory($c));

        $container->glue(
            ObjectModeRepository::class,
            fn (Container $c): ObjectModeRepository => new ObjectModeDBRepository($c->dic()->database())
        );

        $container->glue(
            ToolObjectConfigRepository::class,
            fn (Container $c): ToolObjectConfigRepository => new ToolObjectConfigDBRepository($c->dic()->database())
        );

        $container->glue(
            PersonListGenerators::class,
            fn (Container $c): PersonListGenerators => new PersonListGenerators($c)
        );

        $container->glue(
            AccountListGenerators::class,
            fn (Container $c): AccountListGenerators => new AccountListGenerators($c)
        );

        $container->glue(ObjectInfoProvider::class, fn (Container $c): ObjectInfoProvider => new ObjectInfoProvider(
            $c->dic()->repositoryTree(),
            $c->dic()->ctrl(),
            $c->dic()->http()->request(),
            $c->dic()->rbac()->review()
        ));

        $container->glue(
            UserAccessInfoProvider::class,
            fn (Container $c): UserAccessInfoProvider => new UserAccessInfoProvider(
                $c->dic()->rbac()->system(),
                $c->dic()->rbac()->review(),
                $c->objectInfoProvider()
            )
        );

        return self::$container = $container;
    }
}
