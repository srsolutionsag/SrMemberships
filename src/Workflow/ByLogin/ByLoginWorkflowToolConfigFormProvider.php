<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\ByLogin;

use srag\Plugins\SrMemberships\Workflow\General\AbstractByStringListWorkflowToolConfigFormProvider;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ByLoginWorkflowToolConfigFormProvider extends AbstractByStringListWorkflowToolConfigFormProvider
{
    protected function getPrefix(): string
    {
        return 'by_login';
    }
}
