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

use srag\Plugins\SrMemberships\Person\PersonsToAccounts;
use srag\Plugins\SrMemberships\Container\Container;
use srag\Plugins\SrMemberships\Workflow\Mode\Modes;
use srag\Plugins\SrMemberships\Provider\Context\Context;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
abstract class BaseActionHandler implements ActionHandler
{
    /**
     * @var \srag\Plugins\SrMemberships\Person\Account\AccountListGenerators
     */
    protected $account_list_generators;
    /**
     * @var ActionBuilder
     */
    protected $action_builder;
    /**
     * @var \srag\Plugins\SrMemberships\Person\Persons\PersonListGenerators
     */
    protected $person_list_generators;
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var PersonsToAccounts
     */
    protected $persons_to_accounts;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->persons_to_accounts = new PersonsToAccounts($container->dic()->database());
        $this->person_list_generators = $container->personListGenerators();
        $this->account_list_generators = $container->accountListGenerators();
        $this->action_builder = new ActionBuilder($container);
    }

    protected function generalHandling(
        Context $context,
        \srag\Plugins\SrMemberships\Person\Account\AccountList $account_list,
        Modes $modes
    ) : Summary {
        $current_members = $this->account_list_generators->fromContainerId($context->getCurrentRefId());
        $accounts_to_add = $this->account_list_generators->diff($account_list, $current_members);

        // Subscribe new members
        if ($modes->isModeSet(Modes::RUN_ON_SAVE)) {
            $this->action_builder->subscribe($context->getCurrentRefId())
                                 ->performFor($accounts_to_add);
        }
        $accounts_ro_remove = null;
        if ($modes->isModeSet(Modes::REMOVE_DIFF)) {
            $accounts_ro_remove = $this->account_list_generators->diff($current_members, $account_list);

            // Unsubscribe members that are not in the role anymore
            $this->action_builder->unsubscribe($context->getCurrentRefId())
                                 ->performFor($accounts_ro_remove);
        }

        if ($accounts_to_add->isEmpty() && ($accounts_ro_remove === null || $accounts_ro_remove->isEmpty())) {
            return Summary::empty();
        }

        return Summary::from($accounts_to_add, $accounts_ro_remove);
    }
}
