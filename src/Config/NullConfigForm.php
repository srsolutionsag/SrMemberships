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

use Psr\Http\Message\ServerRequestInterface;
use ILIAS\UI\Component\Input\Container\Form\Standard;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class NullConfigForm extends AbstractConfigForm
{
    public function getForm(
        ?ServerRequestInterface $with_request = null
    ) : Standard {
        return $this->ui_factory->input()->container()->form()->standard(
            '',
            $this->getFields()
        );
    }

    protected function getFields() : array
    {
        return [
            $this->ui_factory->input()->field()->text('null', 'null')
        ];
    }
}
