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
interface CreatablePerson extends Person
{
    public function getEmail(): string;

    public function getLogin(): string;

    public function getAuthMode(): string;

    public function getPassword(): ?string;

    public function getFirstName(): ?string;

    public function getLastName(): ?string;

    public function getGender(): ?string;
}
