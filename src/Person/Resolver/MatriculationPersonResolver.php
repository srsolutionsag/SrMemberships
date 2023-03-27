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

namespace srag\Plugins\SrMemberships\Person\Resolver;

use srag\Plugins\SrMemberships\Person\PersonResolver;
use srag\Plugins\SrMemberships\Person\PersonList;
use srag\Plugins\SrMemberships\Person\PersonSource;
use srag\Plugins\SrMemberships\Person\Persons\MatriculationPerson;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class MatriculationPersonResolver implements PersonResolver
{
    public function resolveFor(PersonSource $source) : PersonList
    {
        $persons = [];
        foreach ($source as $item) {
            $persons[] = new MatriculationPerson($item);
        }

        return new PersonList($persons);
    }
}
