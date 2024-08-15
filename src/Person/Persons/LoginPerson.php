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
class LoginPerson extends BasePerson implements Person
{
    protected string $login;
    public function __construct(
        string $login,
        ?array $attributes = []
    ) {
        $this->login = $login;
        parent::__construct($attributes);
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
