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

use Imager\Color;

trait Colorify
{
    /**
     * @param $color
     * @return $this
     */
    public function colorify($color)
    {
        $this->checkFinalized();

        $color                  = Color::parse($color);

        $this->red($color->getRed());
        $this->green($color->getGreen());
        $this->blue($color->getBlue());
        $this->alpha($color->getAlpha());

        return $this;
    }
}