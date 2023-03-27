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

namespace srag\Plugins\SrMemberships\Person;

use srag\Plugins\SrMemberships\Person\Account\ILIASAccount;
use srag\Plugins\SrMemberships\Person\Persons\UserIdPerson;
use srag\Plugins\SrMemberships\Person\Account\AccountList;
use srag\Plugins\SrMemberships\Person\Persons\Person;
use srag\Plugins\SrMemberships\Person\Persons\PersonList;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class PersonsToAccounts
{
    public function translate(PersonList $person_list): AccountList
    {
        $account_list = new AccountList();
        foreach ($person_list->getPersons() as $person) {
            $user_id = $this->getUserID($person);
            if ($user_id !== null) {
                $account_list->addAccount(new ILIASAccount($user_id));
            }
        }
        return $account_list;
    }

    private function getUserID(Person $person): ?int
    {
        switch (true) {
            case ($person instanceof UserIdPerson):
                return (int) $person->getUniqueIdentification();
            default:
                throw new \InvalidArgumentException("Person " . get_class($person) . " is currently not supported");
        }
    }
}
