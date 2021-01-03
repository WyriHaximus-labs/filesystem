<?php

namespace React\Filesystem;

use React\EventLoop\ExtUvLoop;
use React\EventLoop\LoopInterface;
use React\Filesystem\Uv;
use React\Filesystem\Eio;

final class Factory
{
    public static function create(LoopInterface $loop): FilesystemInterface
    {
        if (\function_exists('uv_loop_new') && $loop instanceof ExtUvLoop) {
            return new Uv\Filesystem($loop);
        }

        if (\function_exists('eio_init')) {
            return new Eio\Filesystem($loop);
        }
    }
}
