<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
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
trait Packer
{
    protected function pack($value): PackedValue
    {
        if (is_string($value)) {
            return new PackedValue($value, PackedValue::TYPE_STRING);
        }
        if (is_array($value)) {
            return new PackedValue(json_encode($value, JSON_THROW_ON_ERROR), PackedValue::TYPE_ARRAY);
        }
        if (is_int($value)) {
            return new PackedValue((string) $value, PackedValue::TYPE_INT);
        }
        if (is_bool($value)) {
            return new PackedValue(($value ? 'true' : 'false'), PackedValue::TYPE_BOOL);
        }
        if (is_null($value)) {
            return new PackedValue('', PackedValue::TYPE_NULL);
        }
        throw new InvalidArgumentException(
            'Only strings, integers and arrays containing those values are allowed, ' . gettype($value) . ' given.'
        );
    }

    protected function unpack(PackedValue $value)
    {
        return match ($value->getType()) {
            PackedValue::TYPE_STRING => $value->getPackedValue(),
            PackedValue::TYPE_INT => (int) $value->getPackedValue(),
            PackedValue::TYPE_BOOL => $value->getPackedValue() === 'true',
            PackedValue::TYPE_ARRAY => json_decode($value->getPackedValue(), true, 512, JSON_THROW_ON_ERROR),
            PackedValue::TYPE_NULL => null,
            default => throw new InvalidArgumentException('Unknown type: ' . $value->getType()),
        };
    }
}
