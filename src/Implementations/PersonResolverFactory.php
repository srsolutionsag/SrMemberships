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

namespace srag\Plugins\SrMemberships\Implementations;

use srag\Plugins\SrMemberships\Person\PersonSource;
use srag\Plugins\SrMemberships\Person\PersonResolver;
use srag\Plugins\SrMemberships\Person\Resolver\EmailPersonResolver;
use srag\Plugins\SrMemberships\Implementations\TextList\ListOfEmailsSource;
use srag\Plugins\SrMemberships\Implementations\TextList\ListOfMatriculationsSource;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class PersonResolverFactory
{
    public function getResolverHandling(PersonSource $source) : PersonResolver
    {
        switch (true) {
            case $source instanceof ListOfMatriculationsSource:
                return new MatriculationPersonResolver();
            case $source instanceof ListOfEmailsSource:
                return new EmailPersonResolver();
            default:
                throw new \InvalidArgumentException('No resolver found for ' . get_class($source));
        }
    }
}
