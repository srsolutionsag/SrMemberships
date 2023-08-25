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
 */

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\Mode;

use srag\Plugins\SrMemberships\Translator;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
abstract class AbstractModes
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

    protected static function getModeTitle(int $mode_id) : string
    {
        $r = new \ReflectionClass(self::class);
        $constants = $r->getConstants();

        foreach ($constants as $constant_name => $constant_value) {
            if ($constant_value === $mode_id) {
                return $constant_name;
            }
        }
        throw new \InvalidArgumentException("Invalid mode id: $mode_id");
    }

    public static function generic(int $mode_id, bool $selectable) : Mode
    {
        return new Mode($mode_id, self::getModeTitle($mode_id), $selectable);
    }

    protected function addMode(Mode $mode) : void
    {
        $this->modes[$mode->getModeId()] = $mode;
        if ($mode->getDependsOn() !== null) {
            $this->addMode($mode->getDependsOn());
        }
    }

    abstract protected function getPrefix() : string;

    public function isModeSet(int $mode_id) : bool
    {
        return isset($this->modes[$mode_id]);
    }

    public function getModes() : array
    {
        return $this->modes;
    }

    public function getModesAsStrings(
        Translator $translator,
        bool $selectable_only = true
    ) : array {
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

    public function getSelectableIntersectedModeIds(Modes $modes) : array
    {
        $to_array = $this->__toArray(true);
        $to_array1 = $modes->__toArray(true);
        return array_intersect($to_array, $to_array1);
    }

    public function __toArray(bool $selectable_only = true) : array
    {
        return array_map(function (Mode $mode) {
            return $mode->getModeId();
        }, array_filter($this->modes, function (Mode $mode) use ($selectable_only) {
            if ($selectable_only) {
                return $mode->isSelectable();
            }
            return true;
        }));
    }
}
