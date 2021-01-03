<?php

namespace React\Filesystem;

use React\EventLoop\ExtUvLoop;
use React\EventLoop\LoopInterface;

final class Stat
{
    private string $path;
    /** @var array<string, mixed> */
    private array $data;

    public function __construct(string $path, array $data)
    {
        $this->path = $path;
        $this->data = $data;
    }

    public function path(): string
    {
        return $this->path;
    }

    /** @return array<string, mixed> */
    public function data(): array
    {
        return $this->data;
    }
}
