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

trait Flush
{
    /**
     * @return $this
     */
    public function flush()
    {
        $this->checkFinalized();

        $this->addCommand(new Command(Save::class, function() {
            return ' {flush}';
        }));

        $builder                    = new self($this->isVerbose(), (string) $this . ' && ');
        $builder->addCommand(new Command(Open::class, function() {
            return ' {flush}';
        }));

        return $builder;
    }
}