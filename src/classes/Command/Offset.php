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

trait Offset
{
    /**
     * @param int $horizontal
     * @param int $vertical
     * @return $this
     */
    public function offset($horizontal = 0, $vertical = 0)
    {
        $this->checkFinalized();

        $command                    = new Command(__TRAIT__, function(Command $command) {
            return ' -geometry ' . (0 <= $command->horizontal ? '+' . $command->horizontal : $command->horizontal) .
            (0 <= $command->vertical ? '+' . $command->vertical : $command->vertical);
        });
        $command->horizontal        = (int) round($horizontal);
        $command->vertical          = (int) round($vertical);

        $this->addCommand($command);
        return $this;
    }
}