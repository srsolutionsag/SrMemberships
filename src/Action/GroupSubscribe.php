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

use srag\Plugins\SrMemberships\Action\Helpers\GroupMembers;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class GroupSubscribe extends AbstractSubscribe
{
    use GroupMembers;
}
