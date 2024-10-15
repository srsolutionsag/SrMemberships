<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
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
     * @readonly
     */
    private UserAccessInfoProvider $user_access_info_provider;
    /**
     * @readonly
     */
    private ObjectInfoProvider $object_info_provider;

    public function __construct(Container $container)
    {
        $this->user_access_info_provider = $container->userAccessInfoProvider();
        $this->object_info_provider = $container->objectInfoProvider();
    }

    public function get(
        int $ref_id,
        int $user_id
    ): Context {
        return new Context(
            $ref_id,
            $user_id,
            $this->user_access_info_provider->hasUserPermissionToAdministrate($user_id, $ref_id),
            $this->object_info_provider->getType($ref_id)
        );
    }
}
