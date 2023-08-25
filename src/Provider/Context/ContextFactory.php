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

namespace srag\Plugins\SrMemberships\Provider\Context;

use srag\Plugins\SrMemberships\Container\Container;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ContextFactory
{

    /**
     * @var UserAccessInfoProvider
     */
    private $user_access_info_provider;
    /**
     * @var ObjectInfoProvider
     */
    private $object_info_provider;

    public function __construct(Container $container)
    {
        $this->user_access_info_provider = $container->userAccessInfoProvider();
        $this->object_info_provider = $container->objectInfoProvider();
    }

    public function get(
        int $ref_id,
        int $user_id
    ) : Context {
        return new Context(
            $ref_id,
            $user_id,
            $this->user_access_info_provider->hasUserPermissionToAdministrate($user_id, $ref_id),
            $this->object_info_provider->getType($ref_id)
        );
    }
}
