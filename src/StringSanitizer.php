<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
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

        return trim(htmlspecialchars(
            strip_tags($string),
            ENT_QUOTES,
            'UTF-8',
            false
        ));
    }
}
