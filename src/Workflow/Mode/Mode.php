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

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 * @internal
 */
final class Mode
{
    private int $mode_id;
    private string $mode_title;
    private bool $is_selectable;
    private ?\srag\Plugins\SrMemberships\Workflow\Mode\Mode $depends_on = null;
    public function __construct(int $mode_id, string $mode_title, bool $is_selectable, ?\srag\Plugins\SrMemberships\Workflow\Mode\Mode $depends_on = null)
    {
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
