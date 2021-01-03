<?php

namespace React\Filesystem\Eio;

use React\EventLoop\ExtUvLoop;
use React\Filesystem\Node\FileInterface;
use React\Filesystem\Stat;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use RuntimeException;
use React\EventLoop\LoopInterface;
use React\Filesystem\Node;
use UV;

trait StatTrait
{
    protected function stat(string $path): PromiseInterface
    {
        return new Promise(function (callable $resolve) use ($path): void {
            $this->activate();
            eio_lstat($path, EIO_PRI_DEFAULT, function ($_, $stat) use ($path, $resolve): void {
                $resolve(new Stat($path, $stat));
                $this->deactivate();
            });
        });
    }
    
    abstract protected function activate(): void;
    abstract protected function deactivate(): void;
}
