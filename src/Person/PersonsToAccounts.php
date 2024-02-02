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
use srag\Plugins\SrMemberships\Person\Persons\LoginPerson;
use srag\Plugins\SrMemberships\Person\Persons\MatriculationPerson;
use srag\Plugins\SrMemberships\Person\Persons\ExtAccountPerson;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class PersonsToAccounts
{
    /**
     * @var \ilDBInterface
     */
    protected $db;

    public function __construct(\ilDBInterface $db)
    {
        $this->db = $db;
    }

    public function translate(PersonList $person_list, bool $remove_found = true) : AccountList
    {
        $account_list = new AccountList();
        foreach ($person_list->getPersons() as $person) {
            $user_id = $this->getUserID($person);
            if ($user_id !== null) {
                $account_list->addAccount(new ILIASAccount($user_id));
                if ($remove_found) {
                    $person_list->removePerson($person);
                }
            }
        }
        return $account_list;
    }

    private function getUserID(Person $person) : ?int
    {
        switch (true) {
            case ($person instanceof UserIdPerson):
                return (int) $person->getUniqueIdentification();
            case ($person instanceof ExtAccountPerson):
                $ext_account = $person->getUniqueIdentification();
                $query = "SELECT usr_id FROM usr_data WHERE ext_account = %s LIMIT 1";

                $result = $this->db->queryF(
                    $query,
                    ['text'],
                    [$ext_account]
                );
                $result = $this->db->fetchAssoc($result);

                if (!empty($result)) {
                    return (int) $result["usr_id"];
                }
                return null;
            case ($person instanceof LoginPerson):
                $login = $person->getUniqueIdentification();
                $looked_up_id = (int) \ilObjUser::_lookupId($login);

                return $looked_up_id > 0 ? $looked_up_id : null;
            case ($person instanceof MatriculationPerson):
                $matriculation = $person->getUniqueIdentification();

                $query = "SELECT usr_id FROM usr_data WHERE matriculation = " . $this->db->quote(
                    $matriculation,
                    "text"
                );
                $result = $this->db->query($query);
                $result = $this->db->fetchAssoc($result);

                if (!empty($result)) {
                    return (int) $result["usr_id"];
                }
                return null;
            default:
                throw new \InvalidArgumentException("Person " . get_class($person) . " is currently not supported");
        }
    }
}
