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

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class RunModes extends AbstractModes
{
    public const RUN_AS_CRONJOB = 4;
    public const RUN_ON_SAVE = 16;

    protected function getPrefix() : string
    {
        return "mode_";
    }

    public static function runAsCronJob() : Mode
    {
        return new Mode(
            self::RUN_AS_CRONJOB,
            self::getModeTitle(self::RUN_AS_CRONJOB),
            true
        );
    }

    public static function runOnSave() : Mode
    {
        return new Mode(
            self::RUN_ON_SAVE,
            self::getModeTitle(self::RUN_ON_SAVE),
            true
        );
    }

    public static function generic(int $mode_id, bool $selectable) : Mode
    {
        switch ($mode_id) {
            case self::RUN_AS_CRONJOB:
                return self::runAsCronJob();
            case self::RUN_ON_SAVE:
                return self::runOnSave();
        }
        return new Mode($mode_id, self::getModeTitle($mode_id), $selectable);
    }
}
