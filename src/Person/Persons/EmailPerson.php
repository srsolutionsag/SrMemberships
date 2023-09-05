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
class EmailPerson implements CreatablePerson
{
    /**
     * @var string
     */
    protected $login;
    /**
     * @var string
     */
    protected $email;

    public function __construct(
        string $email,
        ?string $login = null,
    )
    {
        $this->email = $email;
        $this->login = $login ?? $email;
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
