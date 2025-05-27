<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Exceptions;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class UnsupportedRefIdException extends InvalidRefIdException
{
    public function __construct(int $ref_id, private string $type)
    {
        parent::__construct($ref_id, "Unsupported object type for ref_id " . $ref_id . ": " . $this->type);
    }

    public function getType(): string
    {
        return $this->type;
    }

}
