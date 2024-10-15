<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Container;

use ilSrMembershipsPlugin;
use ilSrMsAccessHandler;
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
use srag\Plugins\SrMemberships\PluginTranslator;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
final class Init
{
    public static function init(
        ilSrMembershipsPlugin $plugin,
        \ilPluginLanguage $language
    ): Container {
        global $DIC;
        $container = new Container();

        $container->glue(ilSrMembershipsPlugin::class, fn(): \ilSrMembershipsPlugin => $plugin);

        $container->glue(
            ilSrMsAccessHandler::class,
            fn(): ilSrMsAccessHandler => new ilSrMsAccessHandler($DIC->rbac(), $DIC->user())
        );

        $container->glue(Configs::class, fn(): Configs => new Configs($DIC->database()));

        $container->glue(Translator::class, fn(): Translator => new PluginTranslator(
            $language
        ));

        $container['_origin'] = fn(): int => ilSrMembershipsDispatcherGUI::getOriginType();

        $container->glue(ilSrMsTabManager::class, fn(Container $c): ilSrMsTabManager => new ilSrMsTabManager(
            $c
        ));

        $container->glue(\ILIAS\DI\Container::class, fn(): \ILIAS\DI\Container => $DIC);

        $container->glue(
            WorkflowContainerRepository::class,
            fn(Container $c): WorkflowContainerRepository => new WorkflowContainerRepository($c)
        );

        $container->glue(ContextFactory::class, fn(Container $c): ContextFactory => new ContextFactory($c));

        $container->glue(
            ObjectModeRepository::class,
            fn(Container $c): ObjectModeRepository => new ObjectModeDBRepository($c->dic()->database())
        );

        $container->glue(
            ToolObjectConfigRepository::class,
            fn(Container $c): ToolObjectConfigRepository => new ToolObjectConfigDBRepository($c->dic()->database())
        );

        $container->glue(
            PersonListGenerators::class,
            fn(Container $c): PersonListGenerators => new PersonListGenerators($c)
        );

        $container->glue(
            AccountListGenerators::class,
            fn(Container $c): AccountListGenerators => new AccountListGenerators($c)
        );

        $container->glue(ObjectInfoProvider::class, fn(Container $c): ObjectInfoProvider => new ObjectInfoProvider(
            $c->dic()->repositoryTree(),
            $c->dic()->ctrl(),
            $c->dic()->http()->request(),
            $c->dic()->rbac()->review()
        ));

        $container->glue(
            UserAccessInfoProvider::class,
            fn(Container $c): UserAccessInfoProvider => new UserAccessInfoProvider(
                $c->dic()->rbac()->system(),
                $c->dic()->rbac()->review(),
                $c->objectInfoProvider()
            )
        );

        return $container;
    }
}
