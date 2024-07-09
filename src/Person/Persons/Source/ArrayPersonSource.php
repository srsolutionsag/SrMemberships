<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Person\Persons\Source;

use Generator;
use srag\Plugins\SrMemberships\StringSanitizer;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ArrayPersonSource implements PersonSource
{
    use StringSanitizer;

    private array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function getRawEntries(): Generator
    {
        foreach ($this->items as $item) {
            yield trim($this->sanitize($item));
        }
    }
}
