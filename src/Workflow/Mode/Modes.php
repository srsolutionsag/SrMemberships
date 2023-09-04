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
 * @author      Fabian Schmid <fabian@sr.solutions>
 */
interface Modes
{
    public static function generic(int $mode_id, bool $selectable) : Mode;

    public function addMode(Mode $mode) : void;

    public function isModeSet(int $mode_id) : bool;

    /**
     * @return Mode[]
     */
    public function getModes() : array;

    public function getModesAsStrings(
        Translator $translator,
        bool $selectable_only = true
    ) : array;

    public function getSelectableIntersectedModeIds(Modes $modes) : array;

    public function __toArray(bool $selectable_only = true) : array;

    public function getDefaultMode() : Mode;
}
