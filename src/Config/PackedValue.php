<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Config;

use InvalidArgumentException;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class PackedValue
{
    private ?string $packed_value;
    public const TYPE_STRING = 1;
    public const TYPE_INT = 2;
    public const TYPE_BOOL = 4;
    public const TYPE_ARRAY = 8;
    public const TYPE_FLOAT = 16;
    public const TYPE_NULL = 32;
    /**
     * @readonly
     */
    private int $type;

    public function __construct(?string $packed_value, int $type)
    {
        $this->packed_value = $packed_value;
        // Check Type
        $this->checkType($type);
        $this->type = $this->packed_value === null ? self::TYPE_NULL : $type;
    }

    private function checkType(int $type): void
    {
        if (!in_array($type, [
            self::TYPE_STRING,
            self::TYPE_INT,
            self::TYPE_BOOL,
            self::TYPE_ARRAY,
            self::TYPE_FLOAT,
            self::TYPE_NULL,
        ])) {
            throw new InvalidArgumentException("Invalid Type");
        }
    }

    public function getPackedValue(): string
    {
        return $this->packed_value;
    }

    public function getType(): int
    {
        return $this->type;
    }
}
