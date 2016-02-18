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

trait HAlign
{
    public function halign($align = 'left')
    {
        $this->checkFinalized();

        $values                     = [
            'left'                  => 'West',
            'center'                => 'Center',
            'right'                 => 'East',
        ];

        if(!in_array($align, array_keys($values), true)) {
            throw new \Exception(sprintf('Invalid horizontal align value, supported values are: %s',
                implode(', ', array_keys($values))));
        }

        if(false !== ($valign = $this->getFirstCommand(VAlign::class))) {
            $valign->gravity        = 'Center' == $valign->gravity ? $values[$align] :
                $valign->gravity . ('Center' == $values[$align] ? '' : $values[$align]);
            return $this;
        }

        $this->gravity($values[$align], __TRAIT__);
        return $this;
    }
}