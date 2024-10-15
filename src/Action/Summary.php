<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Action;

use srag\Plugins\SrMemberships\Translator;
use Throwable;
use srag\Plugins\SrMemberships\Person\Account\AccountList;
use srag\Plugins\SrMemberships\Person\Persons\PersonList;
use srag\Plugins\SrMemberships\Person\Account\Account;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class Summary
{
    private ?AccountList $accounts_added = null;
    private ?AccountList $accounts_removed = null;
    private ?PersonList $persons_not_found = null;
    private const NULL = 0;
    private const OK = 1;
    private const NOK = 2;

    /**
     * @readonly
     */
    private Translator $translator;
    private ?string $header = null;

    private int $status = self::OK;
    private ?string $additional_message = null;

    private bool $show_which = false;

    private function __construct(
        ?AccountList $accounts_added = null,
        ?AccountList $accounts_removed = null,
        ?PersonList $persons_not_found = null
    ) {
        $this->accounts_added = $accounts_added;
        $this->accounts_removed = $accounts_removed;
        $this->persons_not_found = $persons_not_found;
        global $srmembershipsContainer;
        $this->translator = $srmembershipsContainer->translator();
    }

    public function getPersonsNotFound(): PersonList
    {
        return $this->persons_not_found ?? new PersonList();
    }

    public static function empty(): self
    {
        return (new self())->setHeader('result_empty_list')->nok();
    }

    public static function null(): self
    {
        $null = new self();
        $null->status = self::NULL;

        return $null;
    }

    public static function throwable(Throwable $t): self
    {
        global $srmembershipsContainer;
        $txt_key = $t->getMessage();
        $container = $srmembershipsContainer;

        return self::error($container->translator()->txt($txt_key));
    }

    public static function error(string $error_message): self
    {
        return (new self())->setHeader('summary_error')->setAdditionalMessage($error_message)->nok();
    }

    public static function from(
        ?AccountList $accounts_added = null,
        ?AccountList $accounts_removed = null,
        ?PersonList $persons_not_found = null
    ): self {
        return (new self($accounts_added, $accounts_removed, $persons_not_found))->setHeader('summary_ok')->ok();
    }

    protected function setHeader(string $message_key, array $placeholders = []): self
    {
        $this->header = $this->buildStringWithPlaceholder($message_key, $placeholders);
        return $this;
    }

    public function setAdditionalMessage(string $additional_message): self
    {
        $this->additional_message = $additional_message;
        return $this;
    }

    protected function ok(): self
    {
        $this->status = self::OK;
        return $this;
    }

    protected function nok(): self
    {
        $this->status = self::NOK;
        return $this;
    }

    public function getSummary(): string
    {
        $summary = $this->header . "\n\n";

        if ($this->accounts_added instanceof AccountList) {
            $placeholders = [$this->accounts_added->count()];
            $summary .= $this->buildStringWithPlaceholder('accounts_added', $placeholders) . "\n";
            if ($this->show_which) {
                $this->additional_message = implode(
                    "\n",
                    array_map(
                        static fn (Account $account): int => $account->getUserId(),
                        $this->accounts_added->getAccounts()
                    )
                );
            }
        }

        if ($this->accounts_removed instanceof AccountList) {
            $placeholders = [$this->accounts_removed->count()];
            $summary .= $this->buildStringWithPlaceholder('accounts_removed', $placeholders) . "\n";
        }

        if ($this->persons_not_found && $this->persons_not_found->count() > 0) {
            $placeholders = [$this->persons_not_found->count()];
            $summary .= $this->buildStringWithPlaceholder('persons_not_found', $placeholders) . "\n";
            if ($this->show_which) {
                $this->additional_message .= implode(
                    "\n",
                    array_map(
                        static fn (Person $person): string => $person->getUniqueIdentification(),
                        $this->persons_not_found->getPersons()
                    )
                );
            }
        }

        return $summary . ($this->additional_message ?? '');
    }

    public function isNull(): bool
    {
        return $this->status === self::NULL;
    }

    public function isOK(): bool
    {
        return $this->status === self::OK;
    }

    public function isNOK(): bool
    {
        return $this->status !== self::OK;
    }

    protected function buildStringWithPlaceholder(string $message_key, array $placeholders): string
    {
        return sprintf($this->translator->txt($message_key), ...$placeholders);
    }
}
