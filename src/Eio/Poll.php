<?php

namespace React\Filesystem\Eio;

use React\EventLoop\ExtUvLoop;
use React\Filesystem\Node\FileInterface;
use React\Filesystem\PollInterface;
use React\Filesystem\Stat;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use RuntimeException;
use React\EventLoop\LoopInterface;
use React\Filesystem\Node;
use UV;
use function React\Promise\all;

final class Poll implements PollInterface
{
    private LoopInterface $loop;
    private $fd;
    private \Closure $handleEvent;
    private int $workInProgress = 0;

    public function __construct(LoopInterface $loop)
    {
        eio_init();
        $this->fd = eio_get_event_stream();
        $this->loop = $loop;
        $this->handleEvent = function () {
            $this->handleEvent();
        };
    }

    public function activate(): void
    {
        if($this->workInProgress++ === 0) {
            $this->loop->addReadStream($this->fd, $this->handleEvent);
        }
    }

    private function handleEvent()
    {
        if ($this->workInProgress == 0) {
            return;
        }

        while (eio_npending()) {
            eio_poll();
        }
    }

    public function deactivate(): void
    {
        if(--$this->workInProgress <= 0) {
            $this->loop->removeReadStream($this->fd, $this->handleEvent);
        }
    }
}
