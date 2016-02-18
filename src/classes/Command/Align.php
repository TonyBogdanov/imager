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

trait Align
{
    /**
     * @param string $horizontal
     * @param string $vertical
     * @return $this
     */
    public function align($horizontal = 'left', $vertical = 'top')
    {
        $this->checkFinalized();

        $this->halign($horizontal);
        $this->valign($vertical);

        return $this;
    }
}