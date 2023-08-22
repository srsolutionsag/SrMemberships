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
use srag\Plugins\SrMemberships\Workflow\ToolObjectConfig\ToolObjectConfigRepository;
use srag\Plugins\SrMemberships\Person\Persons\PersonListGenerators;
use srag\Plugins\SrMemberships\Person\Account\AccountListGenerators;
use srag\Plugins\SrMemberships\Translator;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
final class Container extends \Pimple\Container
{
    public function glue(string $fqdn, \Closure $factory) : void
    {
        $this[$fqdn] = $this->factory($factory);
    }

    public function get(string $fqdn) : object
    {
        return $this[$fqdn];
    }

    public function dic() : \ILIAS\DI\Container
    {
        return $this->get(\ILIAS\DI\Container::class);
    }

    public function objectInfoProvider() : ObjectInfoProvider
    {
        return $this[ObjectInfoProvider::class];
    }

    public function userAccessInfoProvider() : UserAccessInfoProvider
    {
        return $this[UserAccessInfoProvider::class];
    }

    public function plugin() : \ilSrMembershipsPlugin
    {
        return $this[\ilSrMembershipsPlugin::class];
    }

    public function tabManager() : \ilSrMsTabManager
    {
        return $this[\ilSrMsTabManager::class];
    }

    public function origin() : int
    {
        return $this['_origin'];
    }

    public function config() : Configs
    {
        return $this[Configs::class];
    }

    public function translator() : Translator
    {
        return $this[Translator::class];
    }

    public function accessHandler() : \ilSrMsAccessHandler
    {
        return $this[\ilSrMsAccessHandler::class];
    }

    public function workflows() : WorkflowContainerRepository
    {
        return $this[WorkflowContainerRepository::class];
    }

    public function contextFactory() : ContextFactory
    {
        return $this[ContextFactory::class];
    }

    public function objectModeRepository() : ObjectModeRepository
    {
        return $this[ObjectModeRepository::class];
    }

    public function toolObjectConfigRepository() : ToolObjectConfigRepository
    {
        return $this[ToolObjectConfigRepository::class];
    }

    public function personListGenerators() : PersonListGenerators
    {
        return $this[PersonListGenerators::class];
    }

    public function accountListGenerators() : AccountListGenerators
    {
        return $this[AccountListGenerators::class];
    }
}
