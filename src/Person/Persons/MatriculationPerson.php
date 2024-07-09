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
class MatriculationPerson implements Person
{
    protected string $matriculation;

    public function __construct(string $matriculation)
    {
        $this->matriculation = $matriculation;
    }

    public function getUniqueIdentification(): string
    {
        return $this->matriculation;
    }

    public function isAccountCreatable(): bool
    {
        return false;
    }
}
