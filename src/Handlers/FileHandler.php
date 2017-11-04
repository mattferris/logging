<?php

/**
 * Logging - A PHP logger
 * www.bueller.ca/logging
 *
 * Handlers/FileHandler.php
 * @copyright Copyright (c) 2017
 * @author Matt Ferris <matt@bueller.ca>
 *
 * Licensed under BSD 2-clause license
 * www.bueller.ca/logging/license
 */

namespace MattFerris\Logging\Handlers;

use InvalidArgumentException;
use MattFerris\Logging\MessageInterface;

class FileHandler implements HandlerInterface
{
    /**
     * @var resource File descriptor
     */
    protected $file;

    /**
     * @param string $file Filename to save data to
     */
    public function __construct($file)
    {
        if (!file_exists(dirname($file))) {
            throw new InvalidArgumentException(
                'parent directory '.dirname($file).' not found'
            );
        }

        if (($fd = @fopen($file, 'a')) === false) {
            throw new RuntimeException(
                'failed to open file "'.$file.'"'
            );
        }

        $this->file = $fd;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(MessageInterface $message)
    {
        fwrite($this->file, (string)$message."\n");
    }

    public function __destruct()
    {
        fclose($this->file);
    }
}
