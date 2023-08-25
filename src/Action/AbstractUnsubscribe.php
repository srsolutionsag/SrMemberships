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

namespace srag\Plugins\SrMemberships\Action;

use srag\Plugins\SrMemberships\Person\Account\AccountList;
use srag\Plugins\SrMemberships\Person\Account\Account;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
abstract class AbstractUnsubscribe implements Action
{
    public function performFor(AccountList $accounts) : int
    {
        $counter = 0;
        foreach ($accounts->getAccounts() as $account) {
            $this->removeFromContainer($account);
            $counter++;
        }
        return $counter;
    }

    abstract protected function removeFromContainer(Account $account) : void;
}
