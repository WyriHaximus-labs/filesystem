<?php

namespace React\Tests\Filesystem;

use React\EventLoop\LoopInterface;
use React\Filesystem\FilesystemInterface;
use React\Filesystem\Node\DirectoryInterface;
use React\Filesystem\Node\FileInterface;
use function Clue\React\Block\await;

final class FilesystemTest extends AbstractFilesystemTestCase
{
    /**
     * @test
     *
     * @dataProvider provideFilesystems
     */
    function file(FilesystemInterface $filesystem, LoopInterface $loop): void
    {
        $node = await($filesystem->detect(__FILE__), $loop, 30);

        self::assertInstanceOf(FileInterface::class, $node);
    }

    /**
     * @test
     *
     * @dataProvider provideFilesystems
     */
    function directory(FilesystemInterface $filesystem, LoopInterface $loop): void
    {
        $node = await($filesystem->detect(__DIR__), $loop, 30);

        self::assertInstanceOf(DirectoryInterface::class, $node);
    }
}
