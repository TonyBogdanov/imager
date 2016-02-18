<?php
/**
 * This file is part of the Imager package.
 *
 * (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Imager\Command;

use Imager\Color;

trait Fill
{
    /**
     * @param $color
     * @return $this
     * @throws \Exception
     */
    public function fill($color)
    {
        $this->checkFinalized();

        $command                    = new Command(__TRAIT__, function(Command $command) {
            return ' -fill "' . $command->color->getHEXAString() . '"';
        });
        $command->color             = Color::parse($color);

        $this->addCommand($command);
        return $this;
    }
}