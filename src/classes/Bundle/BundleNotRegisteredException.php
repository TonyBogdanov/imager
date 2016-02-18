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

class BundleNotRegisteredException extends \Exception
{
    /**
     * BundleNotRegisteredException constructor.
     * @param string $bundle
     */
    public function __construct($bundle)
    {
        parent::__construct(sprintf('Bundle "%s" has not been previously registered', $bundle));
    }
}