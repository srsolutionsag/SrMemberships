<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Workflow\ByLogin\Config;

use srag\Plugins\SrMemberships\Workflow\Config\AbstractDBWorkflowConfig;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ByLoginConfig extends AbstractDBWorkflowConfig
{
    public const F_OFFER_WORKFLOW_TO = 'offer_workflow_to';
    public const F_MATCHING_FIELD = 'matching_field';
    public const MATCHING_FIELD_LOGIN = 'login';
    public const MATCHING_FIELD_EXT_ACCOUNT = 'ext_account';

    public function getNameSpace(): string
    {
        return 'by_login';
    }
}
