<?php

namespace React\Filesystem\Eio;

use React\EventLoop\ExtUvLoop;
use React\Filesystem\Node\FileInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use RuntimeException;
use React\EventLoop\LoopInterface;
use React\Filesystem\Node;
use UV;

final class File implements FileInterface
{
    private Poll $poll;
    private string $path;
    private string $name;

    public function __construct(Poll $poll, string $path, string $name)
    {
        $this->poll = $poll;
        $this->path = $path;
        $this->name = $name;
    }

    public function getContents(): PromiseInterface
    {
        $this->activate();
        return new Promise(function (callable $resolve): void {
            eio_open(
                $this->path . DIRECTORY_SEPARATOR . $this->name,
                0,
                0,
                EIO_PRI_DEFAULT,
                function ($_, $fileDescriptor) use ($resolve): void {
                    eio_fstat($fileDescriptor, EIO_PRI_DEFAULT, function ($fileDescriptor, $stat) use ($resolve): void {
                        eio_read($fileDescriptor, (int)$stat['size'], 0, EIO_PRI_DEFAULT, function ($fileDescriptor, string $buffer) use ($resolve): void {
                            $resolve($buffer);
                            eio_close($fileDescriptor, EIO_PRI_DEFAULT, function () {
                                $this->deactivate();
                            });
                        }, $fileDescriptor);
                    }, $fileDescriptor);
                }
            );
        });
    }

    public function putContents(string $contents)
    {
        // TODO: Implement putContents() method.
    }

    public function path(): string
    {
        return $this->path;
    }

    public function name(): string
    {
        return $this->name;
    }

    protected function activate(): void
    {
        $this->poll->activate();
    }

    protected function deactivate(): void
    {
        $this->poll->deactivate();
    }
}
