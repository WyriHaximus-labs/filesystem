<?php

namespace React\Tests\Filesystem;

use React\EventLoop;
use React\EventLoop\LoopInterface;
use React\Filesystem\Factory;
use React\Filesystem\FilesystemInterface;
use React\Filesystem\Node\DirectoryInterface;
use React\Filesystem\Node\FileInterface;
use function Clue\React\Block\await;

final class FactoryTest extends AbstractFilesystemTestCase
{
    /**
     * @test
     */
    function factory(): void
    {
        $loop = EventLoop\Factory::create();
        $node = await(Factory::create($loop)->detect(__FILE__), $loop, 30);

        self::assertInstanceOf(FileInterface::class, $node);
    }

    /**
     * @test
     */
    function streamSelect(): void
    {
        $loop = new EventLoop\StreamSelectLoop();
        $node = await(Factory::create($loop)->detect(__FILE__), $loop, 30);

        self::assertInstanceOf(FileInterface::class, $node);
    }
}
