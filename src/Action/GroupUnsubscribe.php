<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
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
class GroupUnsubscribe extends AbstractUnsubscribe
{
    use GroupMembers;
}
