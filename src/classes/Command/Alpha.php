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

trait Alpha
{
    /**
     * @param $value
     * @param bool $force
     * @return $this
     */
    public function alpha($value, $force = false)
    {
        $this->checkFinalized();

        $value                      = min(1, max(0, $value));

        $command                    = new Command(__TRAIT__, function(Command $command) {
            return ' -channel A -evaluate ' . ($command->force ? 'set ' . ($command->value * 100) . '%' :
                'Multiply ' . $command->value) . ' +channel';
        });
        $command->value             = $value;
        $command->force             = $force;

        $this->addCommand($command);
        return $this;
    }
}