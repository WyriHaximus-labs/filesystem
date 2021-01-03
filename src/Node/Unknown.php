<?php

namespace React\Filesystem\Node;


final class Unknown implements NodeInterface
{
    private string $path;
    private string $name;

    public function __construct(string $path, string $name)
    {
        $this->path = $path;
        $this->name = $name;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function name(): string
    {
        return $this->name;
    }
}
