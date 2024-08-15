<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Person\Persons;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class EmailPerson extends BasePerson implements CreatablePerson
{
    protected string $email;
    protected string $login;

    public function __construct(
        string $email,
        ?string $login = null,
        ?array $attributes = []
    ) {
        $this->email = $email;
        $this->login = $login ?? $this->email;
        parent::__construct($attributes);
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
