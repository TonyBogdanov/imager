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

use Imager\CommandBuilder;

trait Resize
{
    /**
     * @param null $width
     * @param null $height
     * @return $this
     */
    public function resize($width = null, $height = null)
    {
        $this->checkFinalized();

        if(null === $width && null === $height) {
            return $this;
        }

        $command                    = new Command(__TRAIT__, function(Command $command) {
            return ($command->addDensity ? ' -density ' . $command->width : '') . ' -resize ' .
            (0 == $command->width ? '' : $command->width) . 'x' . (0 == $command->height ? '' : $command->height);
        });
        $command->addDensity        = false;
        $command->width             = (int) round($width);
        $command->height            = (int) round($height);

        $this->addFinalizeListener(function(CommandBuilder $builder) use($command) {
            if(false !== ($open = $builder->getFirstCommand(Open::class)) &&
                'svg' == strtolower(pathinfo($open->path, PATHINFO_EXTENSION))) {
                $command->addDensity    = true;
                $command->setPriority($open->getPriority() - 1);
            }
        });

        $this->addCommand($command);
        return $this;
    }
}