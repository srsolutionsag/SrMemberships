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

use srag\Plugins\SrMemberships\Container\Init;
use srag\Plugins\SrMemberships\Person\Account\AccountList;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class Summary
{
    private const OK = 1;
    private const NOK = 2;

    /**
     * @var \srag\Plugins\SrMemberships\Translator
     */
    private $translator;
    /**
     * @var string
     */
    private $message;

    private $status = self::OK;

    private function __construct()
    {
        $container = Init::init($GLOBALS['DIC']);
        $this->translator = $container->translator();
    }

    public static function empty() : self
    {
        return (new self())->setMessage('result_empty_list', [])->nok();
    }

    public static function throwable(\Throwable $t) : self
    {
        return self::error($t->getMessage());
    }

    public static function error(string $error_message) : self
    {
        return (new self())->setMessage('result_error', [$error_message])->nok();
    }

    public static function from(AccountList $accounts_added, ?AccountList $accounts_removed = null) : self
    {
        $placeholders = [
            $accounts_added->count(),
            $accounts_removed === null ? 0 : $accounts_removed->count(),
        ];

        return (new self())->setMessage('result_accounts_added_and_removed', $placeholders)->ok();
    }

    protected function setMessage(string $message_key, array $placeholders) : self
    {
        $this->message = sprintf($this->translator->txt($message_key), ...$placeholders);
        return $this;
    }

    protected function ok() : self
    {
        $this->status = self::OK;
        return $this;
    }

    protected function nok() : self
    {
        $this->status = self::NOK;
        return $this;
    }

    public function getMessage() : string
    {
        return $this->message;
    }

    public function isOK() : bool
    {
        return $this->status === self::OK;
    }
}
