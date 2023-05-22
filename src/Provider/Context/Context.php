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

namespace srag\Plugins\SrMemberships\Provider\Context;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class Context
{
    /**
     * @var bool
     */
    private $user_can_administrate;
    /**
     * @var string
     */
    private $object_type;
    /**
     * @var bool
     */
    protected $is_cli = false;
    /**
     * @var int
     */
    protected $current_ref_id;
    /**
     * @var int
     */
    protected $user_id;

    public function __construct(
        int $current_ref_id,
        int $user_id,
        bool $user_can_administrate,
        string $object_type
    ) {
        $this->current_ref_id = $current_ref_id;
        $this->user_id = $user_id;
        $this->user_can_administrate = $user_can_administrate;
        $this->object_type = $object_type;
        $this->is_cli = (php_sapi_name() === 'cli');
    }

    public function getCurrentRefId(): int
    {
        return $this->current_ref_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @return string|null a valid context type such as
     */
    public function getContextType(): string
    {
        return $this->object_type;
    }

    public function canUserAdministrateMembers(): bool
    {
        return $this->user_can_administrate;
    }

    public function isCli(): bool
    {
        return $this->is_cli;
    }

}
