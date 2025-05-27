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
class InvalidRefIdException extends \InvalidArgumentException
{
    public function __construct(private int $ref_id, string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message ?: "Invalid ref_id: " . $this->ref_id . ".", $code, $previous);
    }

    public function getRefId(): int
    {
        return $this->ref_id;
    }

}
