<?php

use MattFerris\Logging\Logger;
use MattFerris\Logging\MessageInterface;
use MattFerris\Logging\Helpers\HelperInterface;
use MattFerris\Logging\Handlers\HandlerInterface;
use Psr\Log\LogLevel;

class LoggerTest extends PHPUnit\Framework\TestCase
{
    public function testLogWithCatchallHandler()
    {
        $foo = 'bar';
        $handler = function (MessageInterface $message) use (&$foo) {
            $foo = 'baz';
        };

        $logger = new Logger([
            ['handler' => $handler]
        ]);
        $logger->log(LogLevel::ERROR, 'test');

        $this->assertEquals($foo, 'baz');
    }

    public function testLogWithLevelSpecificHandler()
    {
        $foo = 'bar';
        $handler = function (MessageInterface $message) use (&$foo) {
            $foo = 'baz';
        };

        $logger = new Logger([
            ['handler' => $handler, 'levels' => [LogLevel::INFO]]
        ]);

        $logger->log(LogLevel::ERROR, 'test');
        $this->assertEquals($foo, 'bar');

        $logger->log(LogLevel::INFO, 'test');
        $this->assertEquals($foo, 'baz');
    }

    public function testLogWithMaxLevelHandler()
    {
        $foo = 0;
        $handler = function (MessageInterface $message) use (&$foo) {
            $foo++;
        };

        $logger = new Logger([
            ['handler' => $handler, 'maxlevel' => LogLevel::CRITICAL]
        ]);

        $logger->log(LogLevel::EMERGENCY, 'test');
        $this->assertEquals($foo, 1);

        $logger->log(LogLevel::CRITICAL, 'test');
        $this->assertEquals($foo, 2);

        $logger->log(LogLevel::ERROR, 'test');
        $this->assertEquals($foo, 2);
    }

    public function testLogWithObjectHandler()
    {
        $foo = 'bar';
        $handler = $this->createMock(HandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($subject) {
                return $subject instanceof MessageInterface;
            }));

        $logger = new Logger([
            ['handler' => $handler]
        ]);

        $logger->log(LogLevel::ERROR, 'test');
    }

    public function testLogWithCallableHelper()
    {
        $foo = 'bar';
        $helper = function (MessageInterface $message) use (&$foo) {
            $foo = 'baz';
            return $message;
        };
        $handler = function (MessageInterface $message) {};

        $logger = new Logger(
            [['handler' => $handler]],
            [$helper]
        );

        $logger->log(LogLevel::WARNING, 'test');

        $this->assertEquals($foo, 'baz');
    }

    public function testLogWithObjectHelper()
    {
        $message = $this->createMock(MessageInterface::class);
        $message->expects($this->once())
            ->method('getMessage')
            ->willReturn('bar');

        $helper = $this->createMock(HelperInterface::class);
        $helper->expects($this->once())
            ->method('help')
            ->with($this->callback(function ($arg) {
                if ($arg instanceof MessageInterface) {
                    return true;
                } else {
                    return false;
                }
            }))
            ->willReturn($message);

        $handler = $this->createMock(HandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($msg) {
                if ($msg->getMessage() === 'bar') {
                    return true;
                } else {
                    return false;
                }
            }));

        $logger = new Logger(
            [['handler' => $handler]],
            [$helper]
        );

        $logger->log(LogLevel::WARNING, 'foo');
    }
}
