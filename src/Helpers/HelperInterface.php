<?php

/**
 * Logging - A PHP logger
 * www.bueller.ca/logging
 *
 * Helpers/HelperInterface.php
 * @copyright Copyright (c) 2017
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/logging/license
 */

namespace MattFerris\Logging\Helpers;

use MattFerris\Logging\MessageInterface;

interface HelperInterface
{
    /**
     * @param MessageInterface $message The log message
     * @return MessageInterface
     */
    public function help(MessageInterface $message);
}
