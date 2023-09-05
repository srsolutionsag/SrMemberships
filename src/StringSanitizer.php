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

namespace srag\Plugins\SrMemberships;

use ILIAS\Filesystem\Util;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
trait StringSanitizer
{
    public function sanitize(string $string): string
    {
        $string = Util::sanitizeFileName($string);

        // remove some wirdcards
        $string = str_replace(
            [
                '*',
                '?',
                '%',
                '#',
                '@',
                '!',
                '$',
                '&',
                '=',
                '+',
                '~',
                '`',
                '|',
                '{',
                '}',
                '[',
                ']',
                '(',
                ')',
                '<',
                '>',
                ';',
                ':',
                '"',
                "'",
                ',',
                '\\',
                '/',
                "\0",
                "\n",
                "\r",
                "\x0B",
                "\t",
                "\x1A",
            ],
            '',
            $string
        );


        return htmlspecialchars(
            strip_tags($string),
            ENT_QUOTES,
            'UTF-8',
            false
        );
    }
}
