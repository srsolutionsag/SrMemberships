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
        throw new \InvalidArgumentException(
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
                throw new \InvalidArgumentException('Unknown type: ' . $value->getType());
        }
    }
}
