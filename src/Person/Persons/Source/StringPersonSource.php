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

namespace srag\Plugins\SrMemberships\Person\Persons\Source;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class StringPersonSource implements PersonSource
{

    /**
     * @var string
     */
    private $list;

    private $possible_separators = [
        ';',
        ',',
        "\n",
        "\r\n",
        "\t",
    ];

    public function __construct(
        string $list
    ) {
        $this->list = $list;
    }

    private function determineSeparator(string $list) : string
    {
        $separator_count = [
            ';' => 0,
            ',' => 0,
            "\n" => 0,
            "\r\n" => 0,
            "\t" => 0,
        ];
        foreach ($separator_count as $sep => $count) {
            $separator_count[$sep] = substr_count($list, $sep);
        }
        arsort($separator_count);
        $key = key($separator_count);
        if ($separator_count[$key] === 0) {
            throw new \InvalidArgumentException('No separator found in list');
        }
        if (!in_array($key, $this->possible_separators, true)) {
            throw new \InvalidArgumentException(
                'No valid separator found in list, possible separators are: ' . implode(' ', $this->possible_separators)
            );
        }
        return $key;
    }

    public function getRawEntries() : \Generator
    {
        // we try to determine which separator (;, , or \n) is used
        try {
            $separator = $this->determineSeparator($this->list);
        } catch (\InvalidArgumentException $e) {
            $separator = '|||';
        }
        $items = explode($separator, $this->list);
        if (count($items) === 1 && $items[0] === '') {
            throw new \InvalidArgumentException('No items found in list');
        }
        $array_person_source = new ArrayPersonSource($items);
        yield from $array_person_source->getRawEntries();
    }
}
