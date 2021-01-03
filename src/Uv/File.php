<?php

namespace React\Filesystem\Uv;

use React\EventLoop\ExtUvLoop;
use React\Filesystem\Eio\Poll;
use React\Filesystem\Node\FileInterface;
use React\Filesystem\PollInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use RuntimeException;
use React\EventLoop\LoopInterface;
use React\Filesystem\Node;
use UV;

final class File implements FileInterface
{
    private ExtUvLoop $loop;
    private $uvLoop;
    private PollInterface $poll;
    private string $path;
    private string $name;

    public function __construct(PollInterface $poll, ExtUvLoop $loop, string $path, string $name)
    {
        $this->poll = $poll;
        $this->loop = $loop;
        $this->uvLoop = $loop->getUvLoop();
        $this->path = $path;
        $this->name = $name;
    }

    public function getContents(): PromiseInterface
    {
        $this->activate();
        return new Promise(function (callable $resolve): void {
            uv_fs_open($this->uvLoop, $this->path . DIRECTORY_SEPARATOR . $this->name, UV::O_RDONLY, 0, function ($fileDescriptor) use ($resolve): void {
                uv_fs_fstat($this->uvLoop, $fileDescriptor, function ($fileDescriptor, array $stat) use ($resolve): void {
                    uv_fs_read($this->uvLoop, $fileDescriptor, 0, (int)$stat['size'], function ($fileDescriptor, string $buffer) use ($resolve): void {
                        $resolve($buffer);
                        uv_fs_close($this->uvLoop, $fileDescriptor, function () {
                            $this->deactivate();
                        });
                    });
                });
            });
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
