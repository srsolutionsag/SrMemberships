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

use ilSrMsAbstractGUI;
use ILIAS\UI\Component\Input\Container\Form\Standard;
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
        ilSrMsAbstractGUI $target_gui,
        string $target_command,
        Config $config,
        Container $container
    );

    public function getForm(
        ?ServerRequestInterface $with_request = null
    ): Standard;
}
