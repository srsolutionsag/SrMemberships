<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Person\Persons\Resolver;

use srag\Plugins\SrMemberships\Person\Persons\LoginPerson;
use srag\Plugins\SrMemberships\Person\Persons\Source\PersonSource;
use srag\Plugins\SrMemberships\Person\Persons\PersonList;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class LoginPersonResolver implements PersonResolver
{
    public function resolveFor(PersonSource $source): PersonList
    {
        $persons = [];
        foreach ($source->getRawEntries() as $item) {
            $persons[] = new LoginPerson($item);
        }

        return new PersonList($persons);
    }
}
