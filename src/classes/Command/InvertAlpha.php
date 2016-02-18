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

trait InvertAlpha
{
    /**
     * @return $this
     */
    public function invertAlpha()
    {
        $this->checkFinalized();
        $this->addCommand(new Command(__TRAIT__, function() {
            return ' -channel A -fx "1-A" +channel';
        }));
        return $this;
    }
}