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

use srag\Plugins\SrMemberships\Config\AbstractForm;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class Form extends AbstractForm
{
    protected function getFields() : array
    {
        return [
            $this->getMultiSelect(
                GeneralConfig::F_ENABLED_FEATURES,
                $this->translator->txt('enabled_features'),
                [
                    GeneralConfig::BY_EMAIL => $this->translator->txt('feature_by_email'),
                    GeneralConfig::BY_MATRICULATION => $this->translator->txt('feature_by_matriculation'),
                    GeneralConfig::BY_EXCEL_IMPORT => $this->translator->txt('feature_by_excel_import'),
                    GeneralConfig::BY_ATTRIBUTE_SYNC => $this->translator->txt('feature_by_attribute_sync'),
                ],
                $this->translator->txt('enabled_features_info')
            ),
        ];
    }
}
