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

namespace srag\Plugins\SrMemberships\Action;

use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Provider\Context\ObjectInfoProvider;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ActionBuilder
{
    /**
     * @var \srag\Plugins\SrMemberships\Provider\Context\ObjectInfoProvider
     */
    private $object_info;
    /**
     * @var \srag\Plugins\SrMemberships\Container\Container
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->object_info = $container->objectInfoProvider();
    }

    public function subscribe(int $ref_id) : Action
    {
        switch ($this->object_info->getType($ref_id)) {
            case ObjectInfoProvider::TYPE_CRS:
                return new CourseSubscribe($ref_id);
            case ObjectInfoProvider::TYPE_GRP:
                return new GroupSubscribe($ref_id);
            default:
                throw new \InvalidArgumentException('Unsupported object type');
        }
    }

    public function unsubscribe(int $ref_id) : Action
    {
        switch ($this->object_info->getType($ref_id)) {
            case ObjectInfoProvider::TYPE_CRS:
                return new CourseUnsubscribe($ref_id);
            case ObjectInfoProvider::TYPE_GRP:
                return new GroupUnsubscribe($ref_id);
            default:
                throw new \InvalidArgumentException('Unsupported object type');
        }
    }
}
