<?php

namespace React\Tests\Filesystem;

use React\EventLoop\LoopInterface;
use React\Filesystem\FilesystemInterface;
use React\Filesystem\Node\DirectoryInterface;
use React\Filesystem\Node\FileInterface;
use function Clue\React\Block\await;

final class DirectoryTest extends AbstractFilesystemTestCase
{
    /**
     * @test
     *
     * @dataProvider provideFilesystems
     */
    function ls(FilesystemInterface $filesystem, LoopInterface $loop): void
    {
        $expectedListing = [];

        $d = dir(__DIR__);
        while (false !== ($entry = $d->read())) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $expectedListing[__DIR__ . DIRECTORY_SEPARATOR . $entry] = is_file(__DIR__ . DIRECTORY_SEPARATOR . $entry) ? FileInterface::class : DirectoryInterface::class;
        }
        $d->close();

        ksort($expectedListing);

        $directoryListing = await($filesystem->directory(__DIR__)->ls()->toArray()->toPromise(), $loop, 30);

        $listing = [];
        foreach ($directoryListing as $node) {
            $listing[$node->path() . DIRECTORY_SEPARATOR . $node->name()] = $node instanceof FileInterface ? FileInterface::class : DirectoryInterface::class;
        }
        ksort($listing);

        self::assertSame($expectedListing, $listing);
    }
}
