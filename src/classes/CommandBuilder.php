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

use Imager\Command;

class CommandBuilder
{
    use Command\Finalize;
    use Command\Image;
    use Command\Flush;
    use Command\Create;
    use Command\Open;
    use Command\Save;
    use Command\MakeMask;
    use Command\Mask;
    use Command\Resize;
    use Command\Extent;
    use Command\Gravity;
    use Command\Align;
    use Command\HAlign;
    use Command\VAlign;
    use Command\Offset;
    use Command\Flip;
    use Command\Flop;
    use Command\Background;
    use Command\Alpha;
    use Command\Red;
    use Command\Green;
    use Command\Blue;
    use Command\Merge;
    use Command\Colorify;
    use Command\Invert;
    use Command\InvertAlpha;
    use Command\Fill;
    use Command\Rectangle;
    use Command\Text;
    use Command\Label;

    /**
     * @var array
     */
    protected $commands             = [];

    /**
     * @var bool
     */
    protected $verbose              = false;

    /**
     * @var string
     */
    protected $prefix               = '';

    /**
     * @var string
     */
    protected $suffix               = '';

    /**
     * CommandBuilder constructor.
     * @param bool $verbose
     * @param string $prefix
     * @param string $suffix
     */
    public function __construct($verbose = false, $prefix = '', $suffix = '')
    {
        $this->setVerbose($verbose);
        $this->setPrefix($prefix);
        $this->setSuffix($suffix);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $this->finalize();

        /** @var Command\Command $command */
        $result                     = $this->getPrefix() . 'convert' . ($this->isVerbose() ? ' -verbose' : '');
        foreach($this->commands as $command) {
            $result                 .= (string) $command;
        }

        return $result . $this->getSuffix();
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
     * @return CommandBuilder
     */
    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
        return $this;
    }

    /**
     * @return array
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @param array $commands
     * @return CommandBuilder
     */
    public function setCommands($commands)
    {
        $this->commands = $commands;
        return $this;
    }

    /**
     * @param Command\Command $command
     * @return $this
     */
    public function addCommand(Command\Command $command)
    {
        $this->commands[] = $command;
        return $this;
    }

    /**
     * @param null $type
     * @param bool $findFirst
     * @return bool|Command\Command
     */
    public function findCommand($type = null, $findFirst = true)
    {
        if(empty($this->commands)) {
            return false;
        }

        $commands                   = $findFirst ? $this->commands : array_reverse($this->commands);

        if(isset($type)) {
            /** @var Command\Command $command */
            foreach($commands as $command) {
                if($command->getType() === $type) {
                    return $command;
                }
            }
            return false;
        }

        return $commands[0];
    }

    /**
     * @param null $type
     * @return bool|Command\Command
     */
    public function getFirstCommand($type = null)
    {
        return $this->findCommand($type, true);
    }

    /**
     * @param null $type
     * @return bool|Command\Command
     */
    public function getLastCommand($type = null)
    {
        return $this->findCommand($type, false);
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     * @return CommandBuilder
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     * @return CommandBuilder
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
        return $this;
    }
}