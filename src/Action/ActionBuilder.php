<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
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
    protected Container $container;
    /**
     * @readonly
     */
    private ObjectInfoProvider $object_info;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->object_info = $this->container->objectInfoProvider();
    }

    public function subscribe(int $ref_id): Action
    {
        switch ($this->object_info->getType($ref_id)) {
            case ObjectInfoProvider::TYPE_CRS:
                return new CourseSubscribe($ref_id);
            case ObjectInfoProvider::TYPE_GRP:
                return new GroupSubscribe($ref_id);
            default:
                throw new InvalidArgumentException('Unsupported object type');
        }
    }

    public function unsubscribe(int $ref_id): Action
    {
        switch ($this->object_info->getType($ref_id)) {
            case ObjectInfoProvider::TYPE_CRS:
                return new CourseUnsubscribe($ref_id);
            case ObjectInfoProvider::TYPE_GRP:
                return new GroupUnsubscribe($ref_id);
            default:
                throw new InvalidArgumentException('Unsupported object type');
        }
    }
}
