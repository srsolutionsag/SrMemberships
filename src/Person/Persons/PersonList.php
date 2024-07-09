<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Person\Persons;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class PersonList
{
    private array $persons = [];

    public function __construct(array $persons = [])
    {
        foreach ($persons as $person) {
            $this->addPerson($person);
        }
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

    public function count(): int
    {
        return count($this->persons);
    }
}
