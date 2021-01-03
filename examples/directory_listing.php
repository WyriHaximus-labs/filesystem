<?php

use React\EventLoop;
use React\Filesystem\Factory;
use React\Filesystem\Node\NodeInterface;

require 'vendor/autoload.php';

$loop = EventLoop\Factory::create();

Factory::create($loop)->directory(__DIR__)->ls()->subscribe(static function (NodeInterface $node) {
    echo $node->name(), ': ', get_class($node), PHP_EOL;
}, function (Throwable $throwable) {
    echo $throwable;
}, function () {
    echo '----------------------------', PHP_EOL, 'Done listing directory', PHP_EOL;
});

$loop->run();

