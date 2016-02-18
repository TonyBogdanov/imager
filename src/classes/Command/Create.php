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

trait Create
{
    /**
     * @param $width
     * @param $height
     * @param string $background
     * @return $this
     * @throws \Exception
     */
    public function create($width, $height, $background = 'none')
    {
        $this->checkFinalized();

        $command                    = new Command(__TRAIT__, function(Command $command) {
            return ' -size ' . $command->width . 'x' . $command->height . ' xc:' .
            ($command->background->isNone() ? 'none' : '"' . $command->background->getHEXAString() . '"');
        });
        $command->width             = $width;
        $command->height            = $height;
        $command->background        = Color::parse($background);

        $this->addCommand($command);
        return $this;
    }
}