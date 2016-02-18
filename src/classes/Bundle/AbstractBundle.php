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

use Imager\CommandBuilder;
use Imager\Imager;
use Symfony\Component\Console\Command\Command;

abstract class AbstractBundle
{
    /**
     * @var Imager
     */
    protected $imager               = null;

    /**
     * @var array
     */
    protected $optionDefinitions    = null;

    /**
     * @var array
     */
    protected $options              = array();

    /**
     * @var bool
     */
    protected $verbose              = false;

    /**
     * @return array
     */
    protected function getOptionDefinitions()
    {
        return $this->optionDefinitions;
    }

    /**
     * @param $name
     * @return mixed
     * @throws BundleOptionDoesNotExistException
     */
    protected function getOptionDefinition($name)
    {
        if(!$this->hasOption($name)) {
            throw new BundleOptionDoesNotExistException($name, $this->getCanonicalName());
        }

        $definitions                = $this->getOptionDefinitions();

        return $definitions[$name];
    }

    /**
     * @return CommandBuilder
     */
    protected function command()
    {
        return new CommandBuilder($this->isVerbose());
    }

    /**
     * @param array $environment
     * @return mixed
     */
    abstract public function execute(array $environment = array());

    /**
     * AbstractBundle constructor.
     * @param Imager $imager
     */
    public function __construct(Imager $imager)
    {
        $this->setImager($imager);
    }

    /**
     * @return string
     */
    public function getCanonicalName()
    {
        return get_class($this);
    }

    /**
     * @return array
     */
    public function getOptionNames()
    {
        return array_keys($this->getOptionDefinitions());
    }

    /**
     * @param $name
     * @return string
     * @throws BundleOptionDoesNotExistException
     */
    public function getOptionDescription($name)
    {
        if(!$this->hasOption($name)) {
            throw new BundleOptionDoesNotExistException($name, $this->getCanonicalName());
        }

        $definition                 = $this->getOptionDefinition($name);

        return isset($definition['description']) ? $definition['description'] : 'No description';
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasOption($name)
    {
        return in_array($name, $this->getOptionNames(), true);
    }

    /**
     * @param $name
     * @return bool
     * @throws BundleOptionDoesNotExistException
     */
    public function hasOptionValue($name)
    {
        if(!$this->hasOption($name)) {
            throw new BundleOptionDoesNotExistException($name, $this->getCanonicalName());
        }
        return array_key_exists($name, $this->options);
    }

    /**
     * @param $name
     * @return bool
     * @throws BundleOptionDoesNotExistException
     */
    public function hasOptionDefaultValue($name)
    {
        if(!$this->hasOption($name)) {
            throw new BundleOptionDoesNotExistException($name, $this->getCanonicalName());
        }

        $definition                 = $this->getOptionDefinition($name);

        return array_key_exists('default', $definition);
    }

    /**
     * @param $name
     * @return mixed
     * @throws BundleOptionDoesNotExistException
     * @throws BundleOptionHasNoDefaultValueException
     */
    public function getOptionDefaultValue($name)
    {
        if(!$this->hasOptionDefaultValue($name)) {
            throw new BundleOptionHasNoDefaultValueException($name, $this->getCanonicalName());
        }

        $definition                 = $this->getOptionDefinition($name);

        return $definition['default'];
    }

    /**
     * @param $name
     * @return bool
     * @throws BundleOptionDoesNotExistException
     */
    public function hasOptionFilter($name)
    {
        if(!$this->hasOption($name)) {
            throw new BundleOptionDoesNotExistException($name, $this->getCanonicalName());
        }

        $definition                 = $this->getOptionDefinition($name);

        return array_key_exists('filter', $definition) && is_callable($definition['filter']);
    }

    /**
     * @param $name
     * @return callable
     * @throws BundleOptionDoesNotExistException
     * @throws BundleOptionHasNoFilterException
     */
    public function getOptionFilter($name)
    {
        if(!$this->hasOptionFilter($name)) {
            throw new BundleOptionHasNoFilterException($name, $this->getCanonicalName());
        }

        $definition                 = $this->getOptionDefinition($name);

        /** @var callable $filter */
        $filter                     = $definition['filter'];
        return $filter;
    }

    /**
     * @param $name
     * @return bool
     * @throws BundleOptionDoesNotExistException
     */
    public function hasOptionValidator($name)
    {
        if(!$this->hasOption($name)) {
            throw new BundleOptionDoesNotExistException($name, $this->getCanonicalName());
        }

        $definition                 = $this->getOptionDefinition($name);

        return array_key_exists('validator', $definition) && is_callable($definition['validator']);
    }

    /**
     * @param $name
     * @return callable
     * @throws BundleOptionDoesNotExistException
     * @throws BundleOptionHasNoValidatorException
     */
    public function getOptionValidator($name)
    {
        if(!$this->hasOptionValidator($name)) {
            throw new BundleOptionHasNoValidatorException($name, $this->getCanonicalName());
        }

        $definition                 = $this->getOptionDefinition($name);

        /** @var callable $validator */
        $validator                  = $definition['validator'];
        return $validator;
    }

    /**
     * @param $name
     * @param $value
     * @return bool|string
     * @throws BundleOptionDoesNotExistException
     */
    public function setOption($name, $value)
    {
        if(!$this->hasOption($name)) {
            throw new BundleOptionDoesNotExistException($name, $this->getCanonicalName());
        }

        if($this->hasOptionFilter($name)) {
            try {
                $value              = call_user_func($this->getOptionFilter($name), $value);
            } catch(\Exception $e) {
                return $e->getMessage();
            }
        }

        if($this->hasOptionValidator($name)) {
            try {
                $value              = call_user_func($this->getOptionValidator($name), $value);
            } catch(\Exception $e) {
                return $e->getMessage();
            }
        }

        $this->options[$name]       = $value;

        return true;
    }

    /**
     * @param $name
     * @return mixed
     * @throws BundleOptionDoesNotExistException
     * @throws BundleOptionHasNoValueException
     */
    public function getOption($name)
    {
        if(!$this->hasOption($name)) {
            throw new BundleOptionDoesNotExistException($name, $this->getCanonicalName());
        }

        if($this->hasOptionValue($name)) {
            return $this->options[$name];
        }

        if($this->hasOptionDefaultValue($name)) {
            return $this->getOptionDefaultValue($name);
        }

        throw new BundleOptionHasNoValueException($name, $this->getCanonicalName());
    }

    /**
     * @return Imager
     */
    public function getImager()
    {
        return $this->imager;
    }

    /**
     * @param Imager $imager
     * @return AbstractBundle
     */
    public function setImager($imager)
    {
        $this->imager = $imager;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isVerbose()
    {
        return $this->verbose;
    }

    /**
     * @param boolean $verbose
     * @return AbstractBundle
     */
    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
        return $this;
    }
}