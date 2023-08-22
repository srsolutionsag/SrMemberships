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

use srag\Plugins\SrMemberships\Container\Container;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
interface ConfigForm
{
    public const GROUP_KEY_ALL = 'all';
    public const GROUP_KEY_SELECT = 'select';

    public function __construct(
        \ilSrMsAbstractGUI $target_gui,
        string $target_command,
        Config $config,
        Container $container
    );

    public function getForm(
        ?ServerRequestInterface $with_request = null
    ) : \ILIAS\UI\Component\Input\Container\Form\Standard;
}
