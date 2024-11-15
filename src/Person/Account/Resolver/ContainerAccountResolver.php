<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Person\Account\Resolver;

use srag\Plugins\SrMemberships\Person\Account\Source\AccountSource;
use srag\Plugins\SrMemberships\Person\Account\AccountList;
use srag\Plugins\SrMemberships\Person\Account\ILIASAccount;
use srag\Plugins\SrMemberships\Person\Account\Source\RawAccount;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ContainerAccountResolver implements AccountResolver
{
    public function resolveFor(AccountSource $source): AccountList
    {
        $accounts = new AccountList();
        foreach ($source->getEntries() as $raw_account) {
            if ($raw_account->getInternalRole() !== RawAccount::ROLE_MEMBER) {
                continue;
            }

            $accounts->addAccount(new ILIASAccount($raw_account->getUserId(), $raw_account->getInternalRole()));
        }

        return $accounts;
    }
}
