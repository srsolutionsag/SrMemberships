<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Action;

use InvalidArgumentException;
use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Provider\Context\ObjectInfoProvider;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ActionBuilder
{
    /**
     * @readonly
     */
    private ObjectInfoProvider $object_info;

    public function __construct(protected Container $container)
    {
        $this->object_info = $this->container->objectInfoProvider();
    }

    public function subscribe(int $ref_id): Action
    {
        return match ($this->object_info->getType($ref_id)) {
            ObjectInfoProvider::TYPE_CRS => new CourseSubscribe($ref_id),
            ObjectInfoProvider::TYPE_GRP => new GroupSubscribe($ref_id),
            default => throw new InvalidArgumentException('Unsupported object type'),
        };
    }

    public function unsubscribe(int $ref_id): Action
    {
        return match ($this->object_info->getType($ref_id)) {
            ObjectInfoProvider::TYPE_CRS => new CourseUnsubscribe($ref_id),
            ObjectInfoProvider::TYPE_GRP => new GroupUnsubscribe($ref_id),
            default => throw new InvalidArgumentException('Unsupported object type'),
        };
    }
}
