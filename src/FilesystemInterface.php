<?php

namespace React\Filesystem;

use React\Filesystem\Node;
use React\Promise\PromiseInterface;

interface FilesystemInterface
{
    public function file(string $filename): Node\FileInterface;

    public function directory(string $path): Node\DirectoryInterface;

    /**
     * @param string $path
     * @return PromiseInterface<Node\NodeInterface>
     */
    public function detect(string $path): PromiseInterface;
}
