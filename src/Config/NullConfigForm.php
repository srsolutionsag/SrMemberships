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

use Psr\Http\Message\ServerRequestInterface;
use ILIAS\UI\Component\Input\Container\Form\Standard;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class NullConfigForm extends AbstractConfigForm
{
    public function getForm(
        ?ServerRequestInterface $with_request = null
    ): Standard {
        return $this->ui_factory->input()->container()->form()->standard(
            '',
            $this->getFields()
        );
    }

    protected function getFields(): array
    {
        return [
            $this->ui_factory->input()->field()->text('null', 'null')
        ];
    }
}
