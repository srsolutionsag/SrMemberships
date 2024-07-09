<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Person\Persons\Resolver;

use InvalidArgumentException;
use srag\Plugins\SrMemberships\Person\Persons\Source\RolesPersonSource;
use srag\Plugins\SrMemberships\Person\Persons\UserIdPerson;
use srag\Plugins\SrMemberships\Person\Persons\Source\PersonSource;
use srag\Plugins\SrMemberships\Person\Persons\PersonList;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class RolesPersonResolver implements PersonResolver
{
    public function resolveFor(PersonSource $source): PersonList
    {
        if (!$source instanceof RolesPersonSource) {
            throw new InvalidArgumentException('RolesPersonResolver can only resolve RolesPersonSource');
        }
        $list = new PersonList();
        foreach ($source->getRawEntries() as $user_id) {
            $list->addPerson(new UserIdPerson($user_id));
        }
        return $list;
    }
}
