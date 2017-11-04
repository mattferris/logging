<?php

use MattFerris\Logging\Handlers\FileHandler;
use MattFerris\Logging\MessageInterface;
use org\bovigo\vfs\vfsStream;

class FileHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testWriteToFile()
    {
        $msg = '2017-11-04T10:53:02+8:00 [warning] foo';
        $message = $this->createMock(MessageInterface::class);
        $message->expects($this->once())
            ->method('__toString')
            ->willReturn($msg);

        $root = vfsStream::setup('root');
        $handler = new FileHandler(vfsStream::url('root/foo.log'));
        $handler->handle($message);

        $this->assertTrue($root->hasChild('foo.log'));
        $this->assertEquals($msg."\n", file_get_contents(vfsStream::url('root/foo.log')));
    }
}
