<?php

/**
 * Logging - A PHP logger
 * www.bueller.ca/logging
 *
 * Message.php
 * @copyright Copyright (c) 2017
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/logging/license
 */

namespace MattFerris\Logging;

use DateTime;
use InvalidArgumentException;

class Message implements  MessageInterface
{
    /**
     * @var string The log level
     */
    protected $level;

    /**
     * @var string The log message
     */
    protected $message;

    /**
     * @var array The message context
     */
    protected $context;

    /**
     * @var \DateTime The log timestamp
     */
    protected $timestamp;

    /**
     * @param string $level The log level
     * @param string $message The log message
     * @param array $context The message context
     */
    public function __construct($level, $message, array $context)
    {
        $this->level = $level;
        $this->message = $message;
        $this->context = $context;
        $this->timestamp = new DateTime();
    }

    /**
     * @return string The log level
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return string The log message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return MessageInterface
     */
    public function withMessage($message)
    {
        $new = clone $this;
        $new->message = $message;
        return $new;
    }

    /**
     * @return array The message context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array $context
     * @return MessageInterface
     */
    public function withContext(array $context)
    {
        $new = clone $this;
        $new->context = $context;
        return $new;
    }

    /**
     * @return \DateTime The log timestamp
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return string The string representation of the complete log message
     */
    public function __toString()
    {
        return $this->timestamp->format(DateTime::W3C).' ['.$this->level.'] '.$this->getMessage();
    }
}
