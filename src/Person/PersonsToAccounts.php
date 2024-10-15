<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Person;

use ilObjUser;
use InvalidArgumentException;
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
    private const MIN_USR_ID = 13;

    public function __construct(protected \ilDBInterface $db)
    {
    }

    public function translate(PersonList $person_list, bool $remove_found = true): AccountList
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

    public function filterFound(PersonList $person_list): PersonList
    {
        foreach ($person_list->getPersons() as $person) {
            $user_id = $this->getUserID($person);
            if ($user_id !== null) {
                $person_list->removePerson($person);
            }
        }

        return $person_list;
    }

    private function getUserID(Person $person): ?int
    {
        $unique_identification = trim($person->getUniqueIdentification());
        if ($unique_identification === "") {
            return null;
        }

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

                if ($result !== null && $result !== []) {
                    $usr_id = (int) ($result["usr_id"] ?? 0);
                    return $usr_id > self::MIN_USR_ID ? $usr_id : null;
                }
                return null;
            case ($person instanceof LoginPerson):
                $login = $person->getUniqueIdentification();
                $looked_up_id = (int) ilObjUser::_lookupId($login);

                return $looked_up_id > self::MIN_USR_ID ? $looked_up_id : null;
            case ($person instanceof MatriculationPerson):
                $matriculation = $person->getUniqueIdentification();

                $query = "SELECT usr_id FROM usr_data WHERE matriculation = " . $this->db->quote(
                    $matriculation,
                    "text"
                );
                $result = $this->db->query($query);
                $result = $this->db->fetchAssoc($result);

                if ($result !== null && $result !== []) {
                    $usr_id = (int) ($result["usr_id"] ?? 0);
                    return $usr_id > self::MIN_USR_ID ? $usr_id : null;
                }
                return null;
            default:
                throw new InvalidArgumentException("Person " . $person::class . " is currently not supported");
        }
    }
}
