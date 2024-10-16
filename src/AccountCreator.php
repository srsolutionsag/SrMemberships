<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships;

use srag\Plugins\SrMemberships\Person\Account\AccountList;
use srag\Plugins\SrMemberships\Workflow\WorkflowContainer;
use srag\Plugins\SrMemberships\Provider\Context\Context;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class AccountCreator
{
    public function __construct(private readonly WorkflowContainer $workflow_container, private readonly Context $context, private readonly array $data, private readonly array $global_roles, private bool $notify = false)
    {
    }

    public function perform(): AccountList
    {
        global $DIC;
        $new_account = new AccountList();

        foreach ($this->data as $dataset) {
            $user = $this->workflow_container->getActionHandler($this->context)->newUser($dataset);
            $user->setLogin($dataset["login"]);
            $user->setFirstname($dataset["firstname"] ?? "");
            $user->setLastname($dataset["lastname"] ?? "");
            $user->setEmail($dataset["email"]);
            $user->setPasswd($dataset["password"] ?? '');
            $user->setExternalAccount($dataset["ext_account"] ?? '');
            $user->setTimeLimitUnlimited(true);
            $user->setAuthMode(null);
            $user->setActive(true);
            $user->create();
            $user->saveAsNew();

            foreach ($this->global_roles as $global_role) {
                $DIC->rbac()->admin()->assignUser($user->getId(), $global_role);
            }
        }

        return $new_account;
    }

}
