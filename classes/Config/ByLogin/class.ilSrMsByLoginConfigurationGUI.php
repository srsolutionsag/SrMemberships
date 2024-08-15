<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use srag\Plugins\SrMemberships\Workflow\ByLogin\Config\Form;
use srag\Plugins\SrMemberships\Config\General\GeneralConfig;

class ilSrMsByLoginConfigurationGUI extends ilSrMsBaseConfigurationGUI
{
    public function __construct()
    {
        parent::__construct(
            new Form(
                $this,
                self::CMD_SAVE,
                $this->config()->byLogin(),
                $this->container()
            )
        );
    }

    protected function getSubTabId(): string
    {
        return GeneralConfig::BY_LOGIN;
    }
}
