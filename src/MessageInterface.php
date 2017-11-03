<?php

/**
 * Logging - A PHP logger
 * www.bueller.ca/logging
 *
 * MessageInterface.php
 * @copyright Copyright (c) 2017
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/logging/license
 */

namespace MattFerris\Logging;

interface MessageInterface
{
    /**
     * @return string The log level
     */
    public function getLevel();

    /**
     * @return string The log message
     */
    public function getMessage();

    /**
     * @param string $message The message to use for the cloned instance
     * @return MessageInterface
     */
    public function withMessage($message);

    /**
     * @return array The message context
     */
    public function getContext();

    /**
     * @param array $context The context to use for the cloned instance
     * @return MessageInterface
     */
    public function withContext(array $context);

    /**
     * @return \DateTime The log timestamp
     */
    public function getTimestamp();

    /**
     * @return string The string representation of the complete log message
     */
    public function __toString();
}
