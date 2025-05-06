<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Config;

use ilDBInterface;
use srag\Plugins\SrMemberships\Config\General\GeneralConfig;
use srag\Plugins\SrMemberships\Workflow\ByRoleSync\Config\ByRoleSyncConfig;
use srag\Plugins\SrMemberships\Workflow\ByLogin\Config\ByLoginConfig;
use srag\Plugins\SrMemberships\Workflow\ByMatriculation\Config\ByMatriculationConfig;
use srag\Plugins\SrMemberships\Workflow\ByEmail\Config\ByEmailConfig;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
final class Configs
{
    private \ilDBInterface $db;
    /**
     * @readonly
     */
    private GeneralConfig $general;
    /**
     * @readonly
     */
    private ByRoleSyncConfig $by_role_sync;
    /**
     * @readonly
     */
    private ByLoginConfig $by_login;
    /**
     * @readonly
     */
    private ByMatriculationConfig $by_matriculation;
    /**
     * @readonly
     */
    private ByEmailConfig $by_email;

    public function __construct(\ilDBInterface $db)
    {
        $this->db = $db;
        $this->general = new GeneralConfig($this->db);
        $this->by_role_sync = new ByRoleSyncConfig($this->db);
        $this->by_login = new ByLoginConfig($this->db);
        $this->by_matriculation = new ByMatriculationConfig($this->db);
        $this->by_email = new ByEmailConfig($this->db);
    }

    public function general(): GeneralConfig
    {
        return $this->general;
    }

    public function byRoleSync(): ByRoleSyncConfig
    {
        return $this->by_role_sync;
    }

    public function byLogin(): ByLoginConfig
    {
        return $this->by_login;
    }

    public function byEmail(): ByEmailConfig
    {
        return $this->by_email;
    }

    public function byMatriculation(): ByMatriculationConfig
    {
        return $this->by_matriculation;
    }
}
