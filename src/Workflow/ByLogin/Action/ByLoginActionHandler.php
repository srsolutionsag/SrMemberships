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

namespace srag\Plugins\SrMemberships\Workflow\ByLogin\Action;

use srag\Plugins\SrMemberships\Workflow\General\AbstractByStringActionHandler;
use srag\Plugins\SrMemberships\Person\Persons\PersonList;
use srag\Plugins\SrMemberships\Workflow\ByLogin\Config\ByLoginConfig;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ByLoginActionHandler extends AbstractByStringActionHandler
{
    protected function getPersonList(string $text, ?string $original_mime_type = null) : PersonList
    {
        switch ($this->container->config()->byLogin()->get(ByLoginConfig::F_MATCHING_FIELD)) {
            case ByLoginConfig::MATCHING_FIELD_LOGIN:
                return $this->person_list_generators->byLoginsFromString(
                    $text,
                    $original_mime_type
                );
            case ByLoginConfig::MATCHING_FIELD_EXT_ACCOUNT:
                return $this->person_list_generators->byExtAccountsFromString(
                    $text,
                    $original_mime_type
                );
            default:
                throw new \InvalidArgumentException(
                    "Invalid matching field, an administrator must configure the workflow first."
                );
        }
    }
}
