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

trait Image
{
    /**
     * @param $path
     * @param bool $png24
     * @param string $type
     * @param null $priority
     * @return $this
     */
    public function image($path, $png24 = false, $type = __TRAIT__, $priority = null)
    {
        $this->checkFinalized();

        $command                    = new Command($type, function(Command $command) {
            if('png' == strtolower(pathinfo($command->path, PATHINFO_EXTENSION))) {
                return ' PNG' . ($command->png24 ? '24' : '32') . ':"' . $command->path . '"';
            } else {
                return ' "' . $command->path . '"';
            }
        });
        $command->path              = $path;
        $command->png24             = $png24;

        if(isset($priority)) {
            $command->setPriority($priority);
        }

        $this->addCommand($command);
        return $this;
    }
}