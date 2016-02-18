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

trait Background
{
    /**
     * @param string $background
     * @return $this
     */
    public function background($background = 'none')
    {
        $this->checkFinalized();

        $command                    = new Command(__TRAIT__, function(Command $command) {
            return ' -background ' . ($command->background->isNone() ? 'none' :
                '"' . $command->background->getHEXAString() . '"');
        });
        $command->background        = Color::parse($background);

        $this->addCommand($command);
        return $this;
    }
}