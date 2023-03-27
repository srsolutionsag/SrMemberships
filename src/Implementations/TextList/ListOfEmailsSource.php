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

namespace srag\Plugins\SrMemberships\Implementations\TextList;

use srag\Plugins\SrMemberships\Person\PersonSource;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ListOfEmailsSource implements PersonSource
{

    /**
     * @var array|string[]
     */
    private $raw_data;

    public function __construct()
    {
        $this->raw_data = [
            'katharina@sr.solutions',
            'fabian@sr.solutions',
            'marcel@sr.solutions',
            'robin@sr.solutions',
            'thibeau@sr.solutions',
            'lukas@sr.solutions',
            'noreply@sr.solutions'
        ];
    }

    public function getRawEntries() : \Generator
    {
        yield from $this->raw_data;
    }
}
