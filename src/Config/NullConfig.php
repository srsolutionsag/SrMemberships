<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
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
