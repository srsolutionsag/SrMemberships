<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
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
    public function getPersonList(string $text, ?string $original_mime_type = null): PersonList
    {
        return $this->person_list_generators->byMatriculationsFromString(
            $text,
            $original_mime_type
        );
    }

    public function newUser(array $data): \ilObjUser
    {
        $user = parent::newUser($data);
        $user->setMatriculation($data['primary']);
        return $user;
    }

}
