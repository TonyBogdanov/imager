<?php
/**
 * This file is part of the Imager package.
 *
 * (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Imager\Bundle;

class BundleOptionHasNoDefaultValueException extends \Exception
{
    /**
     * BundleOptionHasNoDefaultValueException constructor.
     * @param string $option
     * @param int $bundle
     */
    public function __construct($option, $bundle)
    {
        parent::__construct(sprintf('Bundle "%s" has no default value for option "%s".', $bundle, $option));
    }
}