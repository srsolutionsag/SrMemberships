<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships;

use Closure;
use ILIAS\Refinery\Transformation;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
trait TrafoGenerator
{
    public function trafo(Closure $closure): Transformation
    {
        global $srmembershipsContainer;
        $container = $srmembershipsContainer;
        $refinery = $container->dic()->refinery();

        return $refinery->custom()->transformation($closure);
    }
}
