<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Action;

use srag\Plugins\SrMemberships\Person\Account\AccountList;
use srag\Plugins\SrMemberships\Person\Persons\PersonList;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class UserCreation implements Action
{
    public function performFor(AccountList $accounts): int
    {
        return 0;
    }

    public function createMissing(PersonList $persons): int
    {
    }

}
