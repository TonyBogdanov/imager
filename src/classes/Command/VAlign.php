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

trait VAlign
{
    public function valign($align = 'top')
    {
        $this->checkFinalized();

        $values                     = [
            'top'                   => 'North',
            'middle'                => 'Center',
            'bottom'                => 'South',
        ];

        if(!in_array($align, array_keys($values), true)) {
            throw new \Exception(sprintf('Invalid vertical align value, supported values are: %s',
                implode(', ', array_keys($values))));
        }

        if(false !== ($halign = $this->getFirstCommand(HAlign::class))) {
            $halign->gravity        = 'Center' == $halign->gravity ? $values[$align] :
                ('Center' == $values[$align] ? '' : $values[$align]) . $halign->gravity;
            return $this;
        }

        $this->gravity($values[$align], __TRAIT__);
        return $this;
    }
}