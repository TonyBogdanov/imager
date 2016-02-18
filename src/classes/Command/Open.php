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

trait Open
{
    /**
     * @param string $path
     * @param bool $png24
     * @return string
     */
    public function open($path, $png24 = false)
    {
        if('svg' == strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            $this->background('none');
        }
        $this->image($path, $png24, __TRAIT__);
        return $this;
    }
}