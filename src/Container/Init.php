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

namespace srag\Plugins\SrMemberships\Container;

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
        ?\ilSrMembershipsPlugin $plugin = null
    ) : Container {
        if (isset(self::$container)) {
            return self::$container;
        }
        $container = new Container();

        if ($plugin !== null) {
            $container->glue(\ilSrMembershipsPlugin::class, function () use ($plugin) {
                return $plugin;
            });
        }

        $container->glue(\ilSrMsAccessHandler::class, function () use ($ilias_container) : \ilSrMsAccessHandler {
            return new \ilSrMsAccessHandler($ilias_container->rbac(), $ilias_container->user());
        });

        $container->glue(Configs::class, function () use ($ilias_container) : Configs {
            return new Configs($ilias_container->database());
        });

        $container->glue(Translator::class, function () : Translator {
            return new \ilSrMsTranslator();
        });

        $container['_origin'] = function () : int {
            return \ilSrMembershipsDispatcherGUI::getOriginType();
        };

        $container->glue(\ilSrMsTabManager::class, function (Container $c) : \ilSrMsTabManager {
            return new \ilSrMsTabManager(
                $c
            );
        });

        $container->glue(\ILIAS\DI\Container::class, function () use ($ilias_container) : \ILIAS\DI\Container {
            return $ilias_container;
        });

        $container->glue(WorkflowContainerRepository::class, function (Container $c) : WorkflowContainerRepository {
            return new WorkflowContainerRepository($c);
        });

        $container->glue(ContextFactory::class, function (Container $c) : ContextFactory {
            return new ContextFactory($c);
        });

        $container->glue(ObjectModeRepository::class, function (Container $c) : ObjectModeRepository {
            return new ObjectModeDBRepository($c->dic()->database());
        });

        $container->glue(ToolObjectConfigRepository::class, function (Container $c) : ToolObjectConfigRepository {
            return new ToolObjectConfigDBRepository($c->dic()->database());
        });

        $container->glue(PersonListGenerators::class, function (Container $c) : PersonListGenerators {
            return new PersonListGenerators($c);
        });

        $container->glue(AccountListGenerators::class, function (Container $c) : AccountListGenerators {
            return new AccountListGenerators($c);
        });

        $container->glue(ObjectInfoProvider::class, function (Container $c) : ObjectInfoProvider {
            return new ObjectInfoProvider(
                $c->dic()->repositoryTree(),
                $c->dic()->ctrl(),
                $c->dic()->http()->request(),
                $c->dic()->rbac()->review()
            );
        });

        $container->glue(UserAccessInfoProvider::class, function (Container $c) : UserAccessInfoProvider {
            return new UserAccessInfoProvider(
                $c->dic()->rbac()->system(),
                $c->dic()->rbac()->review(),
                $c->objectInfoProvider()
            );
        });

        return self::$container = $container;
    }
}
