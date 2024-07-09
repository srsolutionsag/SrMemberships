<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Action;

use srag\Plugins\SrMemberships\Person\Account\AccountList;
use srag\Plugins\SrMemberships\Person\Account\Account;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
abstract class AbstractSubscribe implements Action
{
    public function performFor(AccountList $accounts): int
    {
        $counter = 0;
        foreach ($accounts->getAccounts() as $account) {
            $this->addToContainer($account);
            $counter++;
        }
        return $counter;
    }

    abstract protected function addToContainer(Account $account): void;
}
