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

trait Flip
{
    /**
     * @return $this
     */
    public function flip()
    {
        $this->checkFinalized();
        $this->addCommand(new Command(__TRAIT__, function() {
            return ' -flip';
        }));
        return $this;
    }
}