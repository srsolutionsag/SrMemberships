<?php
/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

use ILIAS\DI\RBACServices;

/**
 * This is an abstraction for ILIAS command-class implementations.
 *
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 *
 * The gui-class wraps common dependencies, so that derived classes can
 * slim down their constructor.
 *
 * To enforce the usage of UI components, when rendering content in a
 * derived class, only the method @see ilSrMsAbstractGUI::render() should
 * be used.
 *
 * A notable structural point is, that all derived classes must also implement
 * an index method @see ilSrAbstractGUI::index().
 * The benefit of having an index method is, that redirects to a GUI class
 * can always be made the same, pointing to @see ilSrAbstractGUI::CMD_INDEX.
 *
 * @noinspection AutoloadingIssuesInspection
 */
class ilSrMsAccessHandler
{
    protected RBACServices $access;

    protected \ilObjUser $user;

    /**
     * @param RBACServices $access
     * @param ilObjUser    $user
     */
    public function __construct(
        RBACServices $access,
        ilObjUser $user
    ) {
        $this->access = $access;
        $this->user = $user;
    }

    /**
     * Checks if the current user is assigned the global administrator role.
     *
     * @return bool
     */
    public function isAdministrator(): bool
    {
        return $this->access->review()->isAssigned(
            $this->user->getId(),
            (int) SYSTEM_ROLE_ID
        );
    }

    /**
     * Checks if the current user is administrator of the given object (ref-id).
     *
     * @param int $ref_id
     * @return bool
     */
    public function isAdministratorOf(int $ref_id): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        try {
            $participants = ilParticipants::getInstance($ref_id);
        } catch (InvalidArgumentException $exception) {
            return false;
        }

        return in_array(
            $this->user->getId(),
            $participants->getAdmins(),
            true
        );
    }

    /**
     * Checks if the current user is not logged in (anonymous).
     *
     * @return bool
     */
    public function isAnonymous(): bool
    {
        return (ANONYMOUS_USER_ID === $this->user->getId());
    }

    /**
     * Checks if the given user id matches the current user id.
     *
     * @param int $user_id
     * @return bool
     */
    public function isCurrentUser(int $user_id): bool
    {
        return ($user_id === $this->user->getId());
    }
}
