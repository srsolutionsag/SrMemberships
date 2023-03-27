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

namespace srag\Plugins\SrMemberships\Person\Account\Resolver;

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
            throw new \InvalidArgumentException('Source must be of type CourseAccountSource or GroupAccountSource');
        }

        $accounts = new AccountList();
        foreach ($source->getRawEntries() as $user_id) {
            $accounts->addAccount(new ILIASAccount((int) $user_id));
        }

        return $accounts;
    }

}
