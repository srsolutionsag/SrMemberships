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
class EmailPerson implements CreatablePerson
{
    protected string $email;
    protected string $login;

    public function __construct(string $email, ?string $login = null)
    {
        $this->email = $email;
        $this->login = $login ?? $this->email;
    }

    public function getUniqueIdentification(): string
    {
        return $this->email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getAuthMode(): string
    {
        return 'ilias'; // only local accounts support atm
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getFirstName(): ?string
    {
        return null;
    }

    public function getLastName(): ?string
    {
        return null;
    }

    public function getGender(): ?string
    {
        return null;
    }

    public function isAccountCreatable(): bool
    {
        return true;
    }
}
