<?php

/**
 * Logging - A PHP logger
 * www.bueller.ca/logging
 *
 * Logger.php
 * @copyright Copyright (c) 2017
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/logging/license
 */

namespace MattFerris\Logging;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use MattFerris\Logging\Helpers\HelperInterface;
use MattFerris\Logging\Handlers\HandlerInterface;

class Logger implements LoggerInterface
{
    /**
     * @var array List of configured helpers
     */
    protected $helpers = [];

    /**
     * @var array List of configured handlers
     */
    protected $handlers = [];

    /**
     * @var array Log levels
     */
    protected $levels = [
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG
    ];

    /**
     * @param array $handlers A list of handlers
     * @param array $helpers A list of helpers
     */
    public function __construct(array $handlers, array $helpers = [])
    {
        $this->helpers = $helpers;

        foreach ($handlers as $handler) {
            $levels = [];

            if (isset($handler['maxlevel'])) {

                // register handler with log levels up to the specified one
                $numlvl = count($this->levels);
                for ($i=0; $i<=$numlvl; $i++) {
                    $lvl = $this->levels[$i];
                    $levels[] = $lvl;
                    if ($lvl === $handler['maxlevel']) {
                        break;
                    }
                }

            } elseif (!isset($handler['levels'])) {

                // register handler with all log levels
                $levels = $this->levels;

            } else {

                // register handler with specified levels
                $levels = $handler['levels'];

            }

            foreach ($levels as $level) {
                if (!isset($this->handlers)) {
                    $this->handlers[$level] = [];
                }

                $this->handlers[$level][] = $handler['handler'];
            }
        }
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert($message, array $context = array())
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = array())
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = array())
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = array())
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = array())
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = array())
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = array())
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        // skip if no handlers are defined for the specified level
        if (!isset($this->handlers[$level])) {
            return;
        }

        $message = new Message($level, $message, $context);

        foreach ($this->helpers as $helper) {
            if ($helper instanceof HelperInterface) {
                $data = $helper->help($message);
            } elseif (is_callable($helper)) {
                $data = call_user_func($helper, $message);
            } else {
                throw new Exception(
                    'encountered bad handler, must be callable or implement '.HelperInterface::class
                );
            }
        }

        foreach ($this->handlers[$level] as $handler) {
            if ($handler instanceof HandlerInterface) {
                $handler->handle($message);
            } elseif (is_callable($handler)) {
                call_user_func($handler, $message);
            } else {
                throw new Exception(
                    'encountered bad handler, must be callable or implement '.HandlerInterface::class
                );
            }
        }
    }
}
