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

namespace srag\Plugins\SrMemberships\Config;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class PackedValue
{
    public const TYPE_STRING = 1;
    public const TYPE_INT = 2;
    public const TYPE_BOOL = 4;
    public const TYPE_ARRAY = 8;
    public const TYPE_FLOAT = 16;
    public const TYPE_NULL = 32;

    /**
     * @var string
     */
    private $packed_value;
    /**
     * @var int
     */
    private $type;

    public function __construct(string $packed_value, int $type)
    {
        // Check Type
        $this->checkType($type);

        $this->packed_value = $packed_value;
        $this->type = $type;
    }

    private function checkType(int $type) : void
    {
        if (!in_array($type, [
            self::TYPE_STRING,
            self::TYPE_INT,
            self::TYPE_BOOL,
            self::TYPE_ARRAY,
            self::TYPE_FLOAT,
            self::TYPE_NULL,
        ])) {
            throw new \InvalidArgumentException("Invalid Type");
        }
    }

    public function getPackedValue() : string
    {
        return $this->packed_value;
    }

    public function getType() : int
    {
        return $this->type;
    }
}
