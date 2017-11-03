<?php

/**
 * Logging - A PHP logger
 * www.bueller.ca/logging
 *
 * Handlers/HandlerInterface.php
 * @copyright Copyright (c) 2017
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/logging/license
 */

namespace MattFerris\Logging\Handlers;

use MattFerris\Logging\MessageInterface;

interface HandlerInterface
{
    /**
     * @param MessageInterface $message The log message
     */
    public function handle(MessageInterface $message);
}
