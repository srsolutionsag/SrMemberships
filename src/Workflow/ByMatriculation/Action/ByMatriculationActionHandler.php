<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\ByMatriculation\Action;

use srag\Plugins\SrMemberships\Workflow\General\AbstractByStringActionHandler;
use srag\Plugins\SrMemberships\Person\Persons\PersonList;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ByMatriculationActionHandler extends AbstractByStringActionHandler
{
    protected function getPersonList(string $text, ?string $original_mime_type = null): PersonList
    {
        return $this->person_list_generators->byMatriculationsFromString(
            $text,
            $original_mime_type
        );
    }
}
