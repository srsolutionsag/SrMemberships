<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
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
    /**
     * @var RawPerson[]
     */
    private array $items;

    /**
     * @param RawPerson[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function getRawEntries(): Generator
    {
        foreach ($this->items as $item) {
            yield $item; //trim($this->sanitize((string) $item));
        }
    }
}
