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

namespace srag\Plugins\SrMemberships\Config;

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
     * @throws \InvalidArgumentException if the $value is not of the correct type
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
