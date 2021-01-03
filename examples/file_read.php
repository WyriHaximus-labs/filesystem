<?php

use React\EventLoop;
use React\Filesystem\Factory;

require 'vendor/autoload.php';

$loop = EventLoop\Factory::create();

Factory::create($loop)->file(__FILE__)->getContents()->then(static function (string $contents): void {
    echo $contents;
})->done();

$loop->run();

