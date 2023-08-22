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
        $this->persons_to_accounts = new PersonsToAccounts();
        $this->person_list_generators = $container->personListGenerators();
        $this->account_list_generators = $container->accountListGenerators();
        $this->action_builder = new ActionBuilder($container);
    }
}
