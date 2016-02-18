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

class BundleOptionDoesNotExistException extends \Exception
{
    /**
     * BundleOptionDoesNotExistException constructor.
     * @param string $option
     * @param int $bundle
     */
    public function __construct($option, $bundle)
    {
        parent::__construct(sprintf('Option "%s" does not exist in bundle "%s".', $option, $bundle));
    }
}