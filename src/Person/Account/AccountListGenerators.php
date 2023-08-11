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

namespace srag\Plugins\SrMemberships\Person\Account;

use srag\Plugins\SrMemberships\Container;
use srag\Plugins\SrMemberships\Person\Account\Resolver\ContainerAccountResolver;
use srag\Plugins\SrMemberships\Person\Account\Source\CourseAccountSource;
use srag\Plugins\SrMemberships\Provider\Context\ObjectInfoProvider;
use srag\Plugins\SrMemberships\Person\Account\Source\GroupAccountSource;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class AccountListGenerators
{
    /**
     * @var \srag\Plugins\SrMemberships\Provider\Context\ObjectInfoProvider
     */
    private $object_info;

    public function __construct(Container $container)
    {
        $this->object_info = $container->objectInfoProvider();
    }

    public function fromContainerId(int $ref_id) : AccountList
    {
        $resolver = new ContainerAccountResolver();
        $type = $this->object_info->getType($ref_id);
        switch ($type) {
            case ObjectInfoProvider::TYPE_CRS:
                $source = new CourseAccountSource($ref_id);
                break;
            case ObjectInfoProvider::TYPE_GRP:
                $source = new GroupAccountSource($ref_id);
                break;
            default:
                throw new \InvalidArgumentException('Unsupported object type for ref_id ' . $ref_id . ': ' . $type);
        }

        return $resolver->resolveFor($source);
    }

    public function diff(AccountList $current, AccountList $new) : AccountList
    {
        $diff = new AccountList();
        foreach ($current->getAccounts() as $account) {
            if (!$new->has($account)) {
                $diff->addAccount($account);
            }
        }

        return $diff;
    }
}
