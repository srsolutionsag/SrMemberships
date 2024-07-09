<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Person\Account;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class AccountList
{
    public function __construct(private array $accounts = [])
    {
    }

    public function addAccount(Account $account): void
    {
        if (isset($this->accounts[$account->getUserId()])) {
            return;
        }
        $this->accounts[$account->getUserId()] = $account;
    }

    public function removeAccount(Account $account): void
    {
        if (!isset($this->accounts[$account->getUserId()])) {
            return;
        }
        unset($this->accounts[$account->getUserId()]);
    }

    /**
     * @return Account[]
     */
    public function getAccounts(): array
    {
        return $this->accounts;
    }

    public function has(Account $account): bool
    {
        return isset($this->accounts[$account->getUserId()]);
    }

    public function count(): int
    {
        return count($this->accounts);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }
}
