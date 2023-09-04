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
 * @internal
 */
final class Mode
{
    /**
     * @var string
     */
    private $mode_title;
    /**
     * @var int
     */
    private $mode_id;
    /**
     * @var bool
     */
    private $is_selectable;
    /**
     * @var Mode|null
     */
    private $depends_on;

    public function __construct(
        int $mode_id,
        string $mode_title,
        bool $is_selectable,
        ?Mode $depends_on = null
    ) {
        $this->mode_id = $mode_id;
        $this->mode_title = $mode_title;
        $this->is_selectable = $is_selectable;
        $this->depends_on = $depends_on;
    }

    public function getModeId(): int
    {
        return $this->mode_id;
    }

    public function getModeTitle(): string
    {
        return $this->mode_title;
    }

    public function isSelectable(): bool
    {
        return $this->is_selectable;
    }

    public function getDependsOn(): ?Mode
    {
        return $this->depends_on;
    }
}
