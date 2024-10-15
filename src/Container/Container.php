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

use Closure;
use ilSrMembershipsPlugin;
use ilSrMsTabManager;
use ilSrMsAccessHandler;
use srag\Plugins\SrMemberships\Config\Configs;
use srag\Plugins\SrMemberships\Provider\Context\ObjectInfoProvider;
use srag\Plugins\SrMemberships\Provider\Context\UserAccessInfoProvider;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainerRepository;
use srag\Plugins\SrMemberships\Provider\Context\ContextFactory;
use srag\Plugins\SrMemberships\Workflow\Mode\ObjectModeRepository;
use srag\Plugins\SrMemberships\Workflow\ToolObjectConfig\ToolObjectConfigRepository;
use srag\Plugins\SrMemberships\Person\Persons\PersonListGenerators;
use srag\Plugins\SrMemberships\Person\Account\AccountListGenerators;
use srag\Plugins\SrMemberships\Translator;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
final class Container extends \Pimple\Container
{
    public function glue(string $fqdn, Closure $factory): void
    {
        $this[$fqdn] = $this->factory($factory);
    }

    public function get(string $fqdn): object
    {
        return $this[$fqdn];
    }

    public function dic(): \ILIAS\DI\Container
    {
        return $this->get(\ILIAS\DI\Container::class);
    }

    public function objectInfoProvider(): ObjectInfoProvider
    {
        return $this[ObjectInfoProvider::class];
    }

    public function userAccessInfoProvider(): UserAccessInfoProvider
    {
        return $this[UserAccessInfoProvider::class];
    }

    public function plugin(): ilSrMembershipsPlugin
    {
        return $this[ilSrMembershipsPlugin::class];
    }

    public function tabManager(): ilSrMsTabManager
    {
        return $this[ilSrMsTabManager::class];
    }

    public function origin(): int
    {
        return $this['_origin'];
    }

    public function config(): Configs
    {
        return $this[Configs::class];
    }

    public function translator(): Translator
    {
        return $this[Translator::class];
    }

    public function accessHandler(): ilSrMsAccessHandler
    {
        return $this[ilSrMsAccessHandler::class];
    }

    public function workflows(): WorkflowContainerRepository
    {
        return $this[WorkflowContainerRepository::class];
    }

    public function contextFactory(): ContextFactory
    {
        return $this[ContextFactory::class];
    }

    public function objectModeRepository(): ObjectModeRepository
    {
        return $this[ObjectModeRepository::class];
    }

    public function toolObjectConfigRepository(): ToolObjectConfigRepository
    {
        return $this[ToolObjectConfigRepository::class];
    }

    public function personListGenerators(): PersonListGenerators
    {
        return $this[PersonListGenerators::class];
    }

    public function accountListGenerators(): AccountListGenerators
    {
        return $this[AccountListGenerators::class];
    }
}
