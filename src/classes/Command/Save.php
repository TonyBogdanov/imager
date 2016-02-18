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

trait Save
{
    /**
     * @param string $path
     * @param bool $png24
     * @return string
     */
    public function save($path, $png24 = false)
    {
        $this->image($path, $png24, __TRAIT__, PHP_INT_MAX);
        passthru('(' . str_replace('{flush}', ltrim((string) $this->getLastCommand(), ' '), (string) $this) . ') 2>&1');
        return $path;
    }
}