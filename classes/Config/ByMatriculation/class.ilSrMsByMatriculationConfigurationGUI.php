<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use srag\Plugins\SrMemberships\Workflow\ByMatriculation\Config\Form;
use srag\Plugins\SrMemberships\Config\General\GeneralConfig;

class ilSrMsByMatriculationConfigurationGUI extends ilSrMsBaseConfigurationGUI
{
    public function __construct()
    {
        parent::__construct(
            new Form(
                $this,
                self::CMD_SAVE,
                $this->config()->byMatriculation(),
                $this->container()
            )
        );
    }

    protected function getSubTabId(): string
    {
        return GeneralConfig::BY_MATRICULATION;
    }
}
