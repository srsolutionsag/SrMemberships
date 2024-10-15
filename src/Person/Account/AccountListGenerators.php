<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Person\Account;

use InvalidArgumentException;
use srag\Plugins\SrMemberships\Container\Container;
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
     * @readonly
     */
    private ObjectInfoProvider $object_info;

    public function __construct(Container $container)
    {
        $this->object_info = $container->objectInfoProvider();
    }

    public function fromContainerId(int $ref_id): AccountList
    {
        $resolver = new ContainerAccountResolver();
        $type = $this->object_info->getType($ref_id);
        $source = match ($type) {
            ObjectInfoProvider::TYPE_CRS => new CourseAccountSource($ref_id),
            ObjectInfoProvider::TYPE_GRP => new GroupAccountSource($ref_id),
            default => throw new InvalidArgumentException('Unsupported object type for ref_id ' . $ref_id . ': ' . $type),
        };

        return $resolver->resolveFor($source);
    }

    public function diff(AccountList $current, AccountList $new): AccountList
    {
        $diff = new AccountList();
        foreach ($current->getAccounts() as $account) {
            if (!$new->has($account)) {
                $diff->addAccount($account);
            }
        }

        return $diff;
    }

    public function intersect(AccountList $current, AccountList $new): AccountList
    {
        $intersect = new AccountList();
        // create an account list of accounts which are in both lists
        foreach ($current->getAccounts() as $account) {
            if ($new->has($account)) {
                $intersect->addAccount($account);
            }
        }
        return $intersect;
    }
}
