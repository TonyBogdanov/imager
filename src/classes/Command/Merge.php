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

trait Merge
{
    /**
     * @param $path
     * @param bool $png24
     * @return $this
     */
    public function merge($path, $png24 = false)
    {
        $this->checkFinalized();

        $command                    = new Command(__TRAIT__, function() {
            return ' -composite';
        });

        $this->addFinalizeListener(function(CommandBuilder $builder) use($command) {
            if(false !== ($save = $builder->getFirstCommand(Save::class))) {
                $command->setPriority($save->getPriority() - 1);
            }
        });

        $this->image($path, $png24);

        $this->addCommand($command);
        return $this;
    }
}