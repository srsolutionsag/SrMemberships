<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Person\Persons;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
abstract class BasePerson implements Person
{
    protected ?array $attributes = [];
    public function __construct(?array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function getAdditionalAttributes(): array
    {
        return $this->attributes ?? [];
    }
}
