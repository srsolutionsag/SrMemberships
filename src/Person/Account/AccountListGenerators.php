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
use srag\Plugins\SrMemberships\Exceptions\UnsupportedRefIdException;

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

        switch ($type) {
            case ObjectInfoProvider::TYPE_CRS:
                $source = new CourseAccountSource($ref_id);
                break;
            case ObjectInfoProvider::TYPE_GRP:
                $source = new GroupAccountSource($ref_id);
                break;
            default:
                throw new UnsupportedRefIdException($ref_id, $type);
        }

        return $resolver->resolveFor($source);
    }

    private function syncInternalRole(AccountList $new, AccountList $current): void
    {
        foreach ($current->getAccounts() as $account) {
            if ($new->has($account)) {
                $new->get($account)->setInternalRole($account->getInternalRole());
            }
        }
    }

    public function diff(AccountList $new, AccountList $current): AccountList
    {
        $this->syncInternalRole($new, $current);
        $diff = new AccountList();
        foreach ($new->getAccounts() as $account) {
            if (!$current->has($account)) {
                $diff->addAccount($account);
            }
        }

        return $diff;
    }

    public function intersect(AccountList $new, AccountList $current): AccountList
    {
        $this->syncInternalRole($new, $current);
        $intersect = new AccountList();
        // create an account list of accounts which are in both lists
        foreach ($new->getAccounts() as $account) {
            if ($current->has($account)) {
                $intersect->addAccount($account);
            }
        }
        return $intersect;
    }
}
