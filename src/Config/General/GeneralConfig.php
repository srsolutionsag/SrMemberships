<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Config\General;

use srag\Plugins\SrMemberships\Config\AbstractDBConfig;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class GeneralConfig extends AbstractDBConfig
{
    public const BY_EMAIL = 'by_email';
    public const BY_EXCEL_IMPORT = 'by_excel_import';
    public const BY_ATTRIBUTE_SYNC = 'by_attribute_sync';
    public const BY_ROLE_SYNC = 'by_role_sync';
    public const BY_LOGIN = 'by_login';
    public const BY_MATRICULATION = 'by_matriculation';
    public const F_ENABLED_WORKFLOWS = 'enabled_workflows';

    public function getEnabledWorkflows()
    {
        return $this->get(self::F_ENABLED_WORKFLOWS, []);
    }

    public function getNameSpace(): string
    {
        return 'general';
    }
}
