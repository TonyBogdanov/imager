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

trait MakeMask
{
    /**
     * @param $path
     * @param bool $invert
     * @return mixed
     */
    public function makeMask($path, $invert = false)
    {
        $this->checkFinalized();

        if($invert) {
            $this->invertAlpha();
        }
        $this->colorify('#fff');

        return $this->save($path);
    }
}