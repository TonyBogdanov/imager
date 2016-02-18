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

trait Label
{
    /**
     * @param $caption
     * @param $size
     * @param string $font
     * @return $this
     */
    public function label($caption, $size, $font = 'Arial')
    {
        $this->checkFinalized();

        $command                    = new Command(__TRAIT__, function(Command $command) {
            return ' -font "' . $command->font . '" -pointsize ' . $command->size . ' label:"' .
            str_replace('"', '\"', $command->caption) . '"';
        });
        $command->caption           = $caption;
        $command->size              = (float) $size;
        $command->font              = $font;

        $this->addCommand($command);
        return $this;
    }
}