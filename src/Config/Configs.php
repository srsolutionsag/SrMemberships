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

use srag\Plugins\SrMemberships\Config\General\GeneralConfig;
use srag\Plugins\SrMemberships\Workflow\ByRoleSync\Config\ByRoleSyncConfig;
use srag\Plugins\SrMemberships\Workflow\ByLogin\Config\ByLoginConfig;
use srag\Plugins\SrMemberships\Workflow\ByMatriculation\Config\ByMatriculationConfig;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
final class Configs
{
    /**
     * @var GeneralConfig
     */
    private $general;
    /**
     * @var ByRoleSyncConfig
     */
    private $by_role_sync;
    /**
     * @var ByLoginConfig
     */
    private $by_login;
    /**
     * @var ByMatriculationConfig
     */
    private $by_matriculation;
    /**
     * @var \ilDBInterface
     */
    protected $db;

    public function __construct(\ilDBInterface $db)
    {
        $this->db = $db;
        $this->general = new GeneralConfig($this->db);
        $this->by_role_sync = new ByRoleSyncConfig($this->db);
        $this->by_login = new ByLoginConfig($this->db);
        $this->by_matriculation = new ByMatriculationConfig($this->db);
    }

    public function general() : GeneralConfig
    {
        return $this->general;
    }

    public function byRoleSync() : ByRoleSyncConfig
    {
        return $this->by_role_sync;
    }

    public function byLogin() : ByLoginConfig
    {
        return $this->by_login;
    }

    public function byMatriculation() : ByMatriculationConfig
    {
        return $this->by_matriculation;
    }
}
