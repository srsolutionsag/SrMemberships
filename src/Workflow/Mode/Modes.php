<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\Mode;

use srag\Plugins\SrMemberships\Translator;

/**
 * @author      Fabian Schmid <fabian@sr.solutions>
 */
interface Modes
{
    public static function generic(int $mode_id, bool $selectable): Mode;

    public function addMode(Mode $mode): void;

    public function isModeSet(int $mode_id): bool;

    /**
     * @return Mode[]
     */
    public function getModes(): array;

    public function getModesAsStrings(
        Translator $translator,
        bool $selectable_only = true
    ): array;

    public function getSelectableIntersectedModeIds(Modes $modes): array;

    public function __toArray(bool $selectable_only = true): array;

    public function getDefaultMode(): Mode;
}
