<?php

namespace React\Filesystem\Node;

use React\Promise\PromiseInterface;

interface FileInterface extends NodeInterface
{
    /**
     * Open the file and read all its contents returning those through a promise.
     *
     * @return PromiseInterface<string>
     */
    public function getContents();

    /**
     * Write the given contents to the file, overwriting any existing contents or creating the file.
     *
     * @param string $contents
     * @return PromiseInterface
     */
    public function putContents(string $contents);
}
