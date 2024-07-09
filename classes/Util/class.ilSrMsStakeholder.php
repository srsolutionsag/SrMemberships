<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use ILIAS\ResourceStorage\Stakeholder\AbstractResourceStakeholder;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ilSrMsStakeholder extends AbstractResourceStakeholder
{

    public function __construct()
    {
    }

    public function getId(): string
    {
        return 'srms_plugin';
    }

    public function getOwnerOfNewResources(): int
    {
        return 6;
    }
}
