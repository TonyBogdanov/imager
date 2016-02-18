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

class BundleOptionHasNoValidatorException extends \Exception
{
    /**
     * BundleOptionHasNoValidatorException constructor.
     * @param string $option
     * @param int $bundle
     */
    public function __construct($option, $bundle)
    {
        parent::__construct(sprintf('Option "%s" in bundle "%s" has no valid defined validator.', $option, $bundle));
    }
}