<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Person\Account\Resolver;

use InvalidArgumentException;
use srag\Plugins\SrMemberships\Person\Account\Source\AccountSource;
use srag\Plugins\SrMemberships\Person\Account\AccountList;
use srag\Plugins\SrMemberships\Person\Account\ILIASAccount;
use srag\Plugins\SrMemberships\Person\Account\Source\CourseAccountSource;
use srag\Plugins\SrMemberships\Person\Account\Source\GroupAccountSource;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ContainerAccountResolver implements AccountResolver
{
    public function resolveFor(AccountSource $source): AccountList
    {
        if (!$source instanceof CourseAccountSource && !$source instanceof GroupAccountSource) {
            throw new InvalidArgumentException('Source must be of type CourseAccountSource or GroupAccountSource');
        }

        $accounts = new AccountList();
        foreach ($source->getRawEntries() as $user_id) {
            $accounts->addAccount(new ILIASAccount((int) $user_id));
        }

        return $accounts;
    }
}
