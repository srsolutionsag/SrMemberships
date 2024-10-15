<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\Mode;

use ReflectionClass;
use InvalidArgumentException;
use srag\Plugins\SrMemberships\Translator;

/**
 * @author      Fabian Schmid <fabian@sr.solutions>
 */
abstract class AbstractModes implements Modes
{
    /**
     * @var Mode[]
     */
    protected $modes = [];

    public function __construct(Mode ...$modes)
    {
        foreach ($modes as $mode) {
            $this->addMode($mode);
        }
    }

    protected static function getModeTitle(int $mode_id): string
    {
        $r = new ReflectionClass(static::class);
        $constants = $r->getConstants();

        foreach ($constants as $constant_name => $constant_value) {
            if ($constant_value === $mode_id) {
                return $constant_name;
            }
        }
        throw new InvalidArgumentException("Invalid mode id: $mode_id");
    }

    public static function generic(int $mode_id, bool $selectable): Mode
    {
        return new Mode($mode_id, self::getModeTitle($mode_id), $selectable);
    }

    public function addMode(Mode $mode): void
    {
        $this->modes[$mode->getModeId()] = $mode;
        if ($mode->getDependsOn() instanceof Mode) {
            $this->addMode($mode->getDependsOn());
        }
    }

    abstract protected function getPrefix(): string;

    public function isModeSet(int $mode_id): bool
    {
        return isset($this->modes[$mode_id]);
    }

    public function getModes(): array
    {
        return $this->modes;
    }

    public function getModesAsStrings(
        Translator $translator,
        bool $selectable_only = true
    ): array {
        $modes_as_strings = [];
        foreach ($this->modes as $mode) {
            if ($selectable_only && !$mode->isSelectable()) {
                continue;
            }
            $modes_as_strings[$mode->getModeId()] = $translator->txt(
                $this->getPrefix() . strtolower($mode->getModeTitle())
            );
        }

        return $modes_as_strings;
    }

    public function getSelectableIntersectedModeIds(Modes $modes): array
    {
        $to_array = $this->__toArray(true);
        $to_array1 = $modes->__toArray(true);
        return array_intersect($to_array, $to_array1);
    }

    public function __toArray(bool $selectable_only = true): array
    {
        return array_map(
            static fn(Mode $mode): int => $mode->getModeId(),
            array_filter($this->modes, static function (Mode $mode) use ($selectable_only): bool {
                if ($selectable_only) {
                    return $mode->isSelectable();
                }
                return true;
            })
        );
    }
}
