<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Person\Persons\Source;

use srag\Plugins\SrMemberships\StringSanitizer;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class RawPerson
{
    use StringSanitizer;
    private string $identifier;
    private array $attributes = [];
    public function __construct(
        string $identifier,
        array $attributes = []
    ) {
        $this->identifier = $identifier;
        $this->attributes = $attributes;
        $this->identifier = $this->sanitize($this->identifier);
    }
    public function __toString(): string
    {
        return $this->identifier;
    }
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
