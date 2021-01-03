<?php

namespace React\Filesystem\Node;

use React\Filesystem\AdapterInterface;
use React\Promise\PromiseInterface;
use Rx\Observable;

interface DirectoryInterface extends NodeInterface
{
    public function ls(): Observable;
}
