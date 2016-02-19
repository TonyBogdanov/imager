<?php
/**
 * This file is part of the Imager package.
 *
 * (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Imager;

use Imager\Bundle\AbstractBundle;
use Imager\Bundle\BundleAlreadyRegisteredException;
use Imager\Bundle\BundleNotRegisteredException;
use Imager\Bundle\InvalidBundleException;
use Imager\Bundle\TBCover;
use Imager\Bundle\TBThumbnail;

class Imager
{
    /**
     * @var array
     */
    protected $bundles          = array();

    /**
     * @param $className
     * @return bool|string
     */
    protected function getCanonicalBundleName($className)
    {
        if(!is_string($className)) {
            return false;
        }

        if(!is_a($className, AbstractBundle::class, true)) {
            $className          = implode('\\', array_slice(explode('\\', AbstractBundle::class), 0, -1)) .
                '\\' . ucfirst($className);
            if(!is_a($className, AbstractBundle::class, true)) {
                return false;
            }
        }

        return $className;
    }

    /**
     * Imager constructor.
     */
    public function __construct()
    {
        $this->registerBundle(TBCover::class);
        $this->registerBundle(TBThumbnail::class);
    }

    /**
     * @param $className
     * @return bool
     * @throws InvalidBundleException
     */
    public function isBundleRegistered($className)
    {
        $canonicalName          = $this->getCanonicalBundleName($className);
        if(false === $canonicalName) {
            throw new InvalidBundleException($className);
        }

        return array_key_exists($canonicalName, $this->bundles);
    }

    /**
     * @param $className
     * @param null $path
     * @return $this
     * @throws BundleAlreadyRegisteredException
     * @throws InvalidBundleException
     */
    public function registerBundle($className, $path = null)
    {
        if(is_string($path) && is_file($path)) {
            require_once($path);
        }

        $canonicalName          = $this->getCanonicalBundleName($className);
        if(false === $canonicalName) {
            throw new InvalidBundleException($className);
        }

        if($this->isBundleRegistered($canonicalName)) {
            throw new BundleAlreadyRegisteredException($className);
        }

        $this->bundles[$canonicalName] = new $canonicalName($this);
        return $this;
    }

    /**
     * @param $className
     * @return bool
     */
    public function hasBundle($className)
    {
        try {
            return $this->isBundleRegistered($className);
        } catch(InvalidBundleException $e) {
            return false;
        }
    }

    /**
     * @param $className
     * @return AbstractBundle
     * @throws BundleNotRegisteredException
     * @throws InvalidBundleException
     */
    public function getBundle($className)
    {
        $canonicalName          = $this->getCanonicalBundleName($className);
        if(false === $canonicalName) {
            throw new InvalidBundleException($className);
        }

        if(!$this->isBundleRegistered($canonicalName)) {
            throw new BundleNotRegisteredException($className);
        }

        /** @var AbstractBundle $bundle */
        $bundle                 = $this->bundles[$canonicalName];
        return $bundle;
    }

    /**
     * @return array
     */
    public function getBundles()
    {
        return $this->bundles;
    }

    /**
     * @param $className
     * @param array $options
     * @param array $environment
     * @param bool $verbose
     * @return mixed
     * @throws BundleNotRegisteredException
     * @throws Bundle\BundleOptionDoesNotExistException
     * @throws InvalidBundleException
     */
    public function runBundle($className, array $options = array(), array $environment = array(), $verbose = false)
    {
        $bundle                 = $this->getBundle($className);

        $bundle->setVerbose($verbose);
        foreach($options as $name => $value) {
            $bundle->setOption($name, $value);
        }

        return $bundle->execute($environment);
    }
}