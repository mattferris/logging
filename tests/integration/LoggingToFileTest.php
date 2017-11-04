<?php

use MattFerris\Logging\Logger;
use MattFerris\Logging\Helpers\InterpolationHelper;
use MattFerris\Logging\Handlers\FileHandler;
use org\bovigo\vfs\vfsStream;
use Psr\Log\LogLevel;

class LoggingToFileTest extends PHPUnit_Framework_TestCase
{
    public function testLoggingToFile()
    {
        vfsStream::setup('root');

        $logger = new Logger(
            [
                [
                    'handler' => new FileHandler(vfsStream::url('root/log.txt')),
                    'maxlevel' => LogLevel::NOTICE
                ]
            ],
            [
                new InterpolationHelper()
            ]
        );

        $logger->error('{foo} is broken', ['foo' => 'bar']);

        $this->assertTrue(file_exists(vfsStream::url('root/log.txt')));
    }
}
