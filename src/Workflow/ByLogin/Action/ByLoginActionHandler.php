<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\ByLogin\Action;

use InvalidArgumentException;
use srag\Plugins\SrMemberships\Workflow\General\AbstractByStringActionHandler;
use srag\Plugins\SrMemberships\Person\Persons\PersonList;
use srag\Plugins\SrMemberships\Workflow\ByLogin\Config\ByLoginConfig;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ByLoginActionHandler extends AbstractByStringActionHandler
{
    protected function getPersonList(string $text, ?string $original_mime_type = null): PersonList
    {
        return match ($this->container->config()->byLogin()->get(ByLoginConfig::F_MATCHING_FIELD)) {
            ByLoginConfig::MATCHING_FIELD_LOGIN => $this->person_list_generators->byLoginsFromString(
                $text,
                $original_mime_type
            ),
            ByLoginConfig::MATCHING_FIELD_EXT_ACCOUNT => $this->person_list_generators->byExtAccountsFromString(
                $text,
                $original_mime_type
            ),
            default => throw new InvalidArgumentException(
                "Invalid matching field, an administrator must configure the workflow first."
            ),
        };
    }
}
