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

trait Finalize
{
    /**
     * @var bool
     */
    protected $finalized            = false;

    /**
     * @var array
     */
    protected $finalizeListeners    = [];

    /**
     * @return $this
     */
    protected function finalize()
    {
        if($this->finalized) {
            return $this;
        }

        /** @var Command $command */
        foreach($this->getCommands() as $index => $command) {
            if(null === $command->getPriority()) {
                $command->setPriority($index);
            }
        }

        /** @var callable $listener */
        foreach($this->finalizeListeners as $listener) {
            call_user_func($listener, $this);
        }

        /** @var array $commands */
        $commands                   = $this->getCommands();
        usort($commands, function(Command $left, Command $right) {
            return $left->getPriority() === $right->getPriority() ?
                0 : ($left->getPriority() < $right->getPriority() ? -1 : 1);
        });
        $this->setCommands($commands);

        $this->finalized            = true;
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function checkFinalized()
    {
        if($this->finalized) {
            throw new \Exception('Command already finalized');
        }
        return $this;
    }

    /**
     * @param callable $listener
     * @return $this
     */
    protected function addFinalizeListener(callable $listener)
    {
        $this->finalizeListeners[]  = $listener;
        return $this;
    }
}