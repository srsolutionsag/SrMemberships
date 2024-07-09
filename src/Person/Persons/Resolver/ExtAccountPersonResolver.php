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

use srag\Plugins\SrMemberships\Person\Persons\Source\PersonSource;
use srag\Plugins\SrMemberships\Person\Persons\PersonList;
use srag\Plugins\SrMemberships\Person\Persons\ExtAccountPerson;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ExtAccountPersonResolver implements PersonResolver
{
    public function resolveFor(PersonSource $source): PersonList
    {
        $persons = [];
        foreach ($source->getRawEntries() as $item) {
            $persons[] = new ExtAccountPerson($item);
        }

        return new PersonList($persons);
    }
}
