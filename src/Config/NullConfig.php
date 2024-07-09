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

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class NullConfig implements Config
{
    public function getNameSpace(): string
    {
        return 'null';
    }

    public function set(string $key, $value): void
    {
    }

    public function get(string $key, $default = null)
    {
        return null;
    }

    public function read(): void
    {
    }
}
