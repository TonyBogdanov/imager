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

trait Gravity
{
    /**
     * @param string $gravity
     * @param string $type
     * @return $this
     * @throws \Exception
     */
    public function gravity($gravity = 'None', $type = __TRAIT__)
    {
        $this->checkFinalized();

        $values                     = [
            'None',
            'Forget',
            'Center',
            'East',
            'West',
            'North',
            'South',
            'NorthEast',
            'NorthWest',
            'SouthEast',
            'SouthWest',
        ];

        if(!in_array($gravity, $values, true)) {
            throw new \Exception(sprintf('Invalid gravity value, supported values are: %s', implode(', ', $values)));
        }

        $command                    = new Command($type, function(Command $command) {
            return ' -gravity ' . $command->gravity;
        });
        $command->gravity           = $gravity;

        $this->addCommand($command);
        return $this;
    }
}