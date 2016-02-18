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

class Command
{
    /**
     * @var string
     */
    protected $type                 = null;

    /**
     * @var callable
     */
    protected $stringify            = null;

    /**
     * @var int
     */
    protected $priority             = null;

    /**
     * Command constructor.
     * @param string $type
     * @param callable $stringify
     * @param null $priority
     */
    public function __construct($type, callable $stringify, $priority = null)
    {
        $this->setType($type);
        $this->setStringify($stringify);
        $this->setPriority($priority);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if(!isset($this->stringify)) {
            return '';
        }
        return (string) call_user_func($this->getStringify(), $this);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Command
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return callable
     */
    public function getStringify()
    {
        return $this->stringify;
    }

    /**
     * @param callable $stringify
     * @return Command
     */
    public function setStringify($stringify)
    {
        $this->stringify = $stringify;
        return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     * @return Command
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }
}