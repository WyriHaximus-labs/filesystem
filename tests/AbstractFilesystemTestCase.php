<?php

namespace React\Tests\Filesystem;

use React\EventLoop;
use React\Filesystem\Eio;
use React\Filesystem\Uv;
use PHPUnit\Framework\TestCase;
use React\EventLoop\ExtUvLoop;
use React\EventLoop\LoopInterface;
use React\Filesystem\Factory;
use React\Filesystem\FilesystemInterface;

abstract class AbstractFilesystemTestCase extends TestCase
{
    /**
     * @return iterable<FilesystemInterface>
     */
    final public function provideFilesystems(): iterable
    {
        $loop = EventLoop\Factory::create();
        $streamSelectLoop = new EventLoop\StreamSelectLoop();
        yield 'factory_factory' => [Factory::create($loop), $loop];
        yield 'factory_stream_select' => [Factory::create($streamSelectLoop), $streamSelectLoop];
        if (\function_exists('eio_init')) {
            yield 'eio_factory' => [new Eio\Filesystem($loop), $loop];
            yield 'eio_stream_select' => [new Eio\Filesystem($streamSelectLoop), $streamSelectLoop];
        }
        if (\function_exists('uv_loop_new') && $loop instanceof ExtUvLoop) {
            yield 'uv' => [new Uv\Filesystem($loop), $loop];
        }
    }
}
