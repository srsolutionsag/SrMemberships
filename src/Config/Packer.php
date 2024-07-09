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
        switch ($value->getType()) {
            case PackedValue::TYPE_STRING:
                return $value->getPackedValue();
            case PackedValue::TYPE_INT:
                return (int) $value->getPackedValue();
            case PackedValue::TYPE_BOOL:
                return $value->getPackedValue() === 'true';
            case PackedValue::TYPE_ARRAY:
                return json_decode($value->getPackedValue(), true, 512, JSON_THROW_ON_ERROR);
            case PackedValue::TYPE_NULL:
                return null;
            default:
                throw new InvalidArgumentException('Unknown type: ' . $value->getType());
        }
    }
}
