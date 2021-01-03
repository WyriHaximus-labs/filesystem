<?php

namespace React\Filesystem\Eio;

use React\Filesystem\FilesystemInterface;
use React\EventLoop\LoopInterface;
use React\Filesystem\ModeTypeDetector;
use React\Filesystem\Node;
use React\Filesystem\Node\DirectoryInterface;
use React\Filesystem\Stat;
use React\Promise\PromiseInterface;

final class Filesystem implements FilesystemInterface
{
    use StatTrait;

    private LoopInterface $loop;
    private Poll $poll;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
        $this->poll = new Poll($this->loop);
    }

    public function file(string $path): Node\FileInterface
    {
        return new File($this->poll, dirname($path), basename($path));
    }

    public function directory(string $path): Node\DirectoryInterface
    {
        return new Directory($this->poll, $this, dirname($path), basename($path));
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

    protected function activate(): void
    {
        $this->poll->activate();
    }

    protected function deactivate(): void
    {
        $this->poll->deactivate();
    }
}
