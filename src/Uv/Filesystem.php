<?php

namespace React\Filesystem\Uv;

use React\EventLoop\ExtUvLoop;
use React\Filesystem\FilesystemInterface;
use React\Filesystem\ModeTypeDetector;
use React\Filesystem\PollInterface;
use React\Filesystem\Stat;
use React\Promise\PromiseInterface;
use RuntimeException;
use React\EventLoop\LoopInterface;
use React\Filesystem\Node;

final class Filesystem implements FilesystemInterface
{
    use StatTrait;

    private ExtUvLoop $loop;
    private $uvLoop;
    private PollInterface $poll;

    public function __construct(ExtUvLoop $loop)
    {
        $this->loop = $loop;
        $this->poll = new Poll($this->loop);
        $this->uvLoop = $loop->getUvLoop();
    }

    public function file(string $path): Node\FileInterface
    {
        return new File($this->poll, $this->loop, dirname($path), basename($path));
    }

    public function directory(string $path): Node\DirectoryInterface
    {
        return new Directory($this->poll, $this, $this->loop, dirname($path), basename($path));
    }

    public function detect(string $path): PromiseInterface
    {
        return $this->stat($path)->then(function (Stat $stat) {
            switch (ModeTypeDetector::detect($stat->data()['mode'])) {
                case Node\FileInterface::class:
                    return $this->file($stat->path());
                    break;
                case Node\DirectoryInterface::class:
                    return $this->directory($stat->path());
                    break;
                default:
                    return new Node\Unknown($stat->path(), $stat->path());
                    break;
            }
        });
    }

    protected function uvLoop()
    {
        return $this->uvLoop;
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
