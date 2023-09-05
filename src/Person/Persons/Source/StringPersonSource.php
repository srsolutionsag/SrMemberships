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

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ILIAS\Filesystem\Stream\Streams;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class StringPersonSource implements PersonSource
{
    public const MIME_TEXT_PLAIN = 'text/plain';
    public const MIME_TEXT_CSV = 'text/csv';

    public const MIME_EXCEL = [
        'application/vnd.ms-excel',
        'application/msexcel',
        'application/x-msexcel',
        'application/x-ms-excel',
        'application/x-excel',
        'application/x-dos_ms_excel',
        'application/xls',
        'application/x-xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

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
    /**
     * @var string|null
     */
    private $original_mime_type;

    public function __construct(
        string $list,
        ?string $original_mime_type = null
    ) {
        $this->list = $list;
        $this->original_mime_type = $original_mime_type;
    }

    private function yieldFromCsv() : \Generator
    {
        // first check if there are more than one column in the CSV
        $lines = explode("\n", $this->list);
        $first_line = array_shift($lines);
        try {
            $separator = $this->determineSeparator($first_line);
        } catch (\InvalidArgumentException $e) {
            $separator = ',';
        }

        $first_line = str_getcsv($first_line, $separator);
        if (count($first_line) !== 1) {
            throw new \InvalidArgumentException('msg_error_to_many_columns_in_csv');
        }
        // now read the CSV and add items to the array
        $items = [];
        foreach ($lines as $line) {
            $line = str_getcsv($line, $separator);
            $items[] = $line[0];
        }
        $array_person_source = new ArrayPersonSource($items);
        yield from $array_person_source->getRawEntries();
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
        switch ($this->original_mime_type) {
            case self::MIME_TEXT_PLAIN:
            case null:
                yield from $this->yieldFromPlainText();
                break;
            case self::MIME_TEXT_CSV:
                yield from $this->yieldFromCsv();
            // no break
            default:
                if (in_array($this->original_mime_type, self::MIME_EXCEL, true)) {
                    yield from $this->yieldFromExcel();
                }
                break;
        }
    }

    protected function yieldFromPlainText() : \Generator
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

    private function yieldFromExcel() : \Generator
    {
        $spreadsheet = IOFactory::load($this->list);

        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        $items = [];
        for ($row = 2; $row <= $highestRow; ++$row) {
            $col = 1;
            $cell = $worksheet->getCell([$col, $row]);
            // Skip empty cells
            while (in_array($cell->getValue(), [null, ''], true)) {
                $col++;
                $cell = $worksheet->getCell([$col, $row]);
            }
            $maxCol = $col + 1;
            for (; $col <= $maxCol; ++$col) {
                $value = $worksheet->getCell([$col, $row])->getValue();
                if ($value !== null && $value !== '') {
                    $items[] = $value;
                }
            }
        }

        $array_person_source = new ArrayPersonSource($items);
        yield from $array_person_source->getRawEntries();
    }
}
