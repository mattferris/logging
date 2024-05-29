<?php

use MattFerris\Logging\Helpers\InterpolationHelper;
use MattFerris\Logging\MessageInterface;
use Psr\Log\LogLevel;
use org\bovigo\vfs\vfsStream;

class Helpers_InterpolationHelperTest extends PHPUnit\Framework\TestCase
{
    protected function makeMessage($inmsg, $context, $outmsg)
    {
        $message = $this->createMock(MessageInterface::class);

        $message->expects($this->once())
            ->method('getContext')
            ->willReturn($context);

        $message->expects($this->once())
            ->method('getMessage')
            ->willReturn($inmsg);

        $message->expects($this->once())
            ->method('withMessage')
            ->with($outmsg);

        return $message;
    }

    public function testHelpWithObject()
    {
        $helper = new InterpolationHelper();

        $msg = $this->makeMessage('test {foo}', ['foo' => new stdClass], 'test (stdClass)');
        $helper->help($msg);

        $obj = $this->createMock(Helpers_InterpolationHelperTestFoo::class);
        $obj->expects($this->once())
            ->method('__toString')
            ->willReturn('bar');

        $msg = $this->makeMessage('test {foo}', ['foo' => $obj], 'test bar');
        $helper->help($msg);
    }

    public function testHelpWithArray()
    {
        $helper = new InterpolationHelper();

        $msg = $this->makeMessage('test {foo}', ['foo' => ['bar' => 'baz']], 'test [bar=>"baz"]');
        $helper->help($msg);

        $msg = $this->makeMessage('test {foo}', ['foo' => ['bar' => new stdClass]], 'test [bar=>(stdClass)]');
        $helper->help($msg);

        $msg = $this->makeMessage('test {foo}', ['foo' => ['bar' => ['baz' => 'biz']]], 'test [bar=>[baz=>"biz"]]');
        $helper->help($msg);
    }

    public function testHelpWithInt()
    {
        $helper = new InterpolationHelper();
        $msg = $this->makeMessage('test {foo}', ['foo' => 10], 'test 10');
        $helper->help($msg);
    }

    public function testHelpWithNull()
    {
        $helper = new InterpolationHelper();
        $msg = $this->makeMessage('test {foo}', ['foo' => null], 'test null');
        $helper->help($msg);
    }

    public function testGetMessageWithResource()
    {
        vfsStream::setup('root', null, ['foo' => 'blah']);
        $fd = fopen(vfsStream::url('root/foo'), 'r');

        $helper = new InterpolationHelper();
        $msg = $this->makeMessage('test {foo}', ['foo' => $fd], $this->callback(function ($foo) {
            if (strpos($foo, '(Resource id #') === 5) {
                return true;
            } else {
                return false;
            }
        }));

        $helper->help($msg);

        fclose($fd);
    }
}

class Helpers_InterpolationHelperTestFoo
{
    public function __toString()
    {
    }
}
