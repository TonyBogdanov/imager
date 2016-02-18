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

trait Rectangle
{
    /**
     * @param $x1
     * @param $y1
     * @param $x2
     * @param $y2
     * @return $this
     */
    public function rectangle($x1, $y1, $x2, $y2)
    {
        $this->checkFinalized();

        $command                    = new Command(__TRAIT__, function(Command $command) {
            return ' -draw "rectangle ' . $command->x1 . ',' . $command->y1 . ' ' . $command->x2 . ',' .
            $command->y2 . '"';
        });
        $command->x1                = (int) round($x1);
        $command->y1                = (int) round($y1);
        $command->x2                = (int) round($x2);
        $command->y2                = (int) round($y2);

        $this->addCommand($command);
        return $this;
    }
}