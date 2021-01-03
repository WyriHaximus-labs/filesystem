<?php

namespace React\Tests\Filesystem;

use React\EventLoop\LoopInterface;
use React\Filesystem\FilesystemInterface;
use function Clue\React\Block\await;

final class FileTest extends AbstractFilesystemTestCase
{
    /**
     * @test
     *
     * @dataProvider provideFilesystems
     */
    function getContents(FilesystemInterface $filesystem, LoopInterface $loop): void
    {
        $fileContents = await($filesystem->file(__FILE__)->getContents(), $loop, 30);

        self::assertSame(file_get_contents(__FILE__), $fileContents);
    }
}
