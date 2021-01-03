<?php

namespace React\Filesystem\Uv;

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
            uv_fs_stat($this->uvLoop(), $path, function (array $stat) use ($path, $resolve): void {
                $resolve(new Stat($path, $stat));
                $this->deactivate();
            });
        });
    }
    
    abstract protected function uvLoop(): void;
    abstract protected function activate(): void;
    abstract protected function deactivate(): void;
}
