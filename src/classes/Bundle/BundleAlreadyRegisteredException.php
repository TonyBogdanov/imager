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

class BundleAlreadyRegisteredException extends \Exception
{
    /**
     * BundleAlreadyRegisteredException constructor.
     * @param string $bundle
     */
    public function __construct($bundle)
    {
        parent::__construct(sprintf('Bundle "%s" has already been previously registered', $bundle));
    }
}