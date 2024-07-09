<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Config;

use InvalidArgumentException;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
interface Config
{
    public function getNameSpace(): string;

    /**
     * @param string                $key
     * @param string|bool|int|array $value
     * @return void
     * @throws InvalidArgumentException if the $value is not of the correct type
     */
    public function set(string $key, $value): void;

    /**
     * @param string                $key
     * @param string|bool|int|array $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    public function read(): void;
}
