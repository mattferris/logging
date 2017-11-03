<?php

use MattFerris\Logging\Message;
use Psr\Log\LogLevel;
use org\bovigo\vfs\vfsStream;

class MessageTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $message = new Message(LogLevel::ERROR, 'test {foo}', ['foo' => 'bar']);
        $this->assertEquals(LogLevel::ERROR, $message->getLevel());
        $this->assertEquals('test {foo}', $message->getMessage());
        $this->assertEquals(['foo' => 'bar'], $message->getContext());
        $this->assertInstanceOf(\DateTime::class, $message->getTimestamp());

        $level = $message->getLevel();
        $msg = $message->getMessage();
        $ts = $message->getTimestamp()->format(\DateTime::ATOM);;

        $this->assertEquals($ts.' ['.$level.'] '.$msg, (string)$message);
    }
}
