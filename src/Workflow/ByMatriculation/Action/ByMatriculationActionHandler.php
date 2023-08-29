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

namespace srag\Plugins\SrMemberships\Workflow\ByMatriculation\Action;

use srag\Plugins\SrMemberships\Workflow\General\AbstractByStringActionHandler;
use srag\Plugins\SrMemberships\Person\Persons\PersonList;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ByMatriculationActionHandler extends AbstractByStringActionHandler
{
    protected function getPersonList(string $text, ?string $original_mime_type = null) : PersonList
    {
        return $this->person_list_generators->byMatriculationsFromString(
            $text,
            $original_mime_type
        );
    }
}
