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

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class PersonList
{
    private $persons = [];

    public function __construct(array $persons = [])
    {
        $this->persons = $persons;
    }

    public function addPerson(Person $person): void
    {
        if (isset($this->persons[$person->getUniqueIdentification()])) {
            return;
        }
        $this->persons[$person->getUniqueIdentification()] = $person;
    }

    public function removePerson(Person $person): void
    {
        if (!isset($this->persons[$person->getUniqueIdentification()])) {
            return;
        }
        unset($this->persons[$person->getUniqueIdentification()]);
    }

    /**
     * @return Person[]
     */
    public function getPersons(): array
    {
        return $this->persons;
    }
}
