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
class Modes
{
    public const NONE = 0;
    public const AD_HOC = 1;
    public const CRON = 2;
    public const RUN_AS_CRONJOB = 4;
    public const REMOVE_DIFF = 8;
    public const RUN_ON_SAVE = 16;
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

    private static function getModeTitle(int $mode_id) : string
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

    public static function cron() : Mode
    {
        return new Mode(self::CRON, self::getModeTitle(self::CRON), false);
    }

    public static function adHoc() : Mode
    {
        return new Mode(self::AD_HOC, self::getModeTitle(self::AD_HOC), false);
    }

    public static function removeDiff() : Mode
    {
        return new Mode(self::REMOVE_DIFF, self::getModeTitle(self::REMOVE_DIFF), true);
    }

    public static function runAsCronJob() : Mode
    {
        return new Mode(
            self::RUN_AS_CRONJOB,
            self::getModeTitle(self::RUN_AS_CRONJOB),
            true,
            self::cron()
        );
    }

    public static function runOnSave() : Mode
    {
        return new Mode(
            self::RUN_ON_SAVE,
            self::getModeTitle(self::RUN_ON_SAVE),
            true,
            self::adHoc()
        );
    }

    public static function generic(int $mode_id, bool $selectable) : Mode
    {
        switch ($mode_id) {
            case self::CRON:
                return self::cron();
            case self::AD_HOC:
                return self::adHoc();
            case self::RUN_AS_CRONJOB:
                return self::runAsCronJob();
            case self::REMOVE_DIFF:
                return self::removeDiff();
        }
        return new Mode($mode_id, self::getModeTitle($mode_id), $selectable);
    }

    protected function addMode(Mode $mode) : void
    {
        $this->modes[$mode->getModeId()] = $mode;
        if ($mode->getDependsOn() !== null) {
            $this->addMode($mode->getDependsOn());
        }
    }

    public function isModeSet(int $mode_id) : bool
    {
        return isset($this->modes[$mode_id]);
    }

    public function isCron() : bool
    {
        return $this->isModeSet(self::CRON);
    }

    public function isAdHoc() : bool
    {
        return $this->isModeSet(self::AD_HOC);
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
            $modes_as_strings[$mode->getModeId()] = $translator->txt('mode_' . strtolower($mode->getModeTitle()));
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
