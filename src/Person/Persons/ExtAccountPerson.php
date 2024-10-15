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
class ExtAccountPerson extends BasePerson implements Person
{
    public function __construct(
        protected string $ext_account,
        ?array $attributes = []
    ) {
        parent::__construct($attributes);
    }

    public function getUniqueIdentification(): string
    {
        return $this->ext_account;
    }

    public function isAccountCreatable(): bool
    {
        return false;
    }
}
