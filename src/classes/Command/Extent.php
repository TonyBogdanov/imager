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

trait Extent
{
    /**
     * @param null $width
     * @param null $height
     * @return $this
     */
    public function extent($width = null, $height = null)
    {
        $this->checkFinalized();

        $command                    = new Command(__TRAIT__, function(Command $command) {
            return ' -extent ' . (0 == $command->width ? '' : $command->width) . 'x' .
            (0 == $command->height ? '' : $command->height);
        });
        $command->width             = (int) round($width);
        $command->height            = (int) round($height);

        $this->addFinalizeListener(function(CommandBuilder $builder) use($command) {
            if(!$builder->getFirstCommand(Background::class)) {
                $builder->background();
                $builder->getLastCommand()->setPriority($command->getPriority() - 1);
            }
            if(false !== ($halign = $builder->getFirstCommand(HAlign::class))) {
                $halign->setPriority($command->getPriority() - 1);
            }
            if(false !== ($valign = $builder->getFirstCommand(VAlign::class))) {
                $valign->setPriority($command->getPriority() - 1);
            }
        });

        $this->addCommand($command);
        return $this;
    }
}