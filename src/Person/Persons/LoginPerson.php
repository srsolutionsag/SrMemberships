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
class LoginPerson implements Person
{
    protected string $login;

    public function __construct(string $login)
    {
        $this->login = $login;
    }

    public function getUniqueIdentification(): string
    {
        return $this->login;
    }

    public function isAccountCreatable(): bool
    {
        return false;
    }
}
