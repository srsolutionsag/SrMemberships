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

namespace srag\Plugins\SrMemberships\Person\Persons;

use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Person\Persons\Resolver\RolesPersonResolver;
use srag\Plugins\SrMemberships\Person\Persons\Source\RolesPersonSource;
use srag\Plugins\SrMemberships\Person\Resolver\LoginPersonResolver;
use srag\Plugins\SrMemberships\Person\Persons\Source\StringPersonSource;
use srag\Plugins\SrMemberships\Person\Persons\Source\ArrayPersonSource;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class PersonListGenerators
{

    /**
     * @var \srag\Plugins\SrMemberships\Container\Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function byRoleIds(array $role_ids) : PersonList
    {
        return (new RolesPersonResolver())->resolveFor(
            new RolesPersonSource($role_ids, $this->container->dic()->rbac()->review())
        );
    }

    public function byLogins(array $logins) : PersonList
    {
        return (new LoginPersonResolver())->resolveFor(
            new ArrayPersonSource($logins)
        );
    }

    public function byLoginsFromString(string $logins) : PersonList
    {
        return (new LoginPersonResolver())->resolveFor(
            new StringPersonSource($logins)
        );
    }
}
