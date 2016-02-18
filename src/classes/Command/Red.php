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

trait Red
{
    /**
     * @param $value
     * @return $this
     */
    public function red($value)
    {
        $this->checkFinalized();

        $value                      = min(1, max(0, $value));

        $command                    = new Command(__TRAIT__, function(Command $command) {
            return ' -channel R -evaluate set ' . (100 * $command->value) . '% +channel';
        });
        $command->value             = $value;

        $this->addCommand($command);
        return $this;
    }
}