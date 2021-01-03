<?php

namespace React\Filesystem\Node;

interface NodeInterface
{
    const DS = DIRECTORY_SEPARATOR;

    /**
     * Return the full path, for example: /path/to/file.ext
     *
     * @return string
     */
    public function path();

    /**
     * Return the node name, for example: file.ext
     *
     * @return string
     */
    public function name();
}
