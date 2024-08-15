<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Person\Persons;

use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Person\Persons\Resolver\RolesPersonResolver;
use srag\Plugins\SrMemberships\Person\Persons\Source\RolesPersonSource;
use srag\Plugins\SrMemberships\Person\Persons\Resolver\LoginPersonResolver;
use srag\Plugins\SrMemberships\Person\Persons\Source\StringPersonSource;
use srag\Plugins\SrMemberships\Person\Persons\Source\ArrayPersonSource;
use srag\Plugins\SrMemberships\Person\Persons\Resolver\MatriculationPersonResolver;
use srag\Plugins\SrMemberships\Person\Persons\Resolver\ExtAccountPersonResolver;
use srag\Plugins\SrMemberships\Person\Persons\Source\RawPerson;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class PersonListGenerators
{
    /**
     * @readonly
     */
    private Container $container;
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function byRoleIds(array $role_ids): PersonList
    {
        return (new RolesPersonResolver())->resolveFor(
            new RolesPersonSource($role_ids, $this->container->dic()->rbac()->review())
        );
    }

    public function byLogins(array $logins): PersonList
    {
        return (new LoginPersonResolver())->resolveFor(
            new ArrayPersonSource(array_map(static fn (string $login): RawPerson => new RawPerson($login), $logins))
        );
    }

    public function byLoginsFromString(string $logins, ?string $original_mime_type = null): PersonList
    {
        return (new LoginPersonResolver())->resolveFor(
            new StringPersonSource($logins, $original_mime_type)
        );
    }

    public function byExtAccountsFromString(string $logins, ?string $original_mime_type = null): PersonList
    {
        return (new ExtAccountPersonResolver())->resolveFor(
            new StringPersonSource($logins, $original_mime_type)
        );
    }

    public function byMatriculationsFromString(string $matriculations, ?string $original_mime_type = null): PersonList
    {
        return (new MatriculationPersonResolver())->resolveFor(
            new StringPersonSource($matriculations, $original_mime_type)
        );
    }
}
