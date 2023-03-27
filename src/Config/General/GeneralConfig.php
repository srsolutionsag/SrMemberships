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
    public const BY_MATRICULATION = 'by_matriculation';
    public const BY_ROLE_SYNC = 'by_role_sync';
    public const F_ENABLED_WORKFLOWS = 'enabled_workflows';

    public function getEnabledWorkflows()
    {
        return $this->get(self::F_ENABLED_WORKFLOWS, []);
    }

    public function isByEmailEnabled(): bool
    {
        return in_array(self::BY_EMAIL, $this->getEnabledWorkflows(), true);
    }

    public function isByRoleSyncEnabled(): bool
    {
        return in_array(self::BY_ROLE_SYNC, $this->getEnabledWorkflows(), true);
    }

    public function isByMatriculationEnabled(): bool
    {
        return in_array(self::BY_MATRICULATION, $this->getEnabledWorkflows(), true);
    }

    public function isByExcelImportEnabled(): bool
    {
        return in_array(self::BY_EXCEL_IMPORT, $this->getEnabledWorkflows(), true);
    }

    public function isByAttributeSyncEnabled(): bool
    {
        return in_array(self::BY_ATTRIBUTE_SYNC, $this->getEnabledWorkflows(), true);
    }

    public function getNameSpace(): string
    {
        return 'general';
    }
}
