<?php

namespace React\Filesystem\Uv;

use React\EventLoop\ExtUvLoop;
use React\Filesystem\FilesystemInterface;
use React\Filesystem\Node;
use React\Filesystem\PollInterface;
use React\Promise\CancellablePromiseInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use Rx\Observable;
use Rx\Subject\Subject;
use UV;
use function React\Promise\all;

final class Directory implements Node\DirectoryInterface
{
    use StatTrait;

    private ExtUvLoop $loop;
    private $uvLoop;
    private PollInterface $poll;
    private FilesystemInterface $filesystem;
    private string $path;
    private string $name;

    public function __construct(PollInterface $poll, FilesystemInterface $filesystem, ExtUvLoop $loop, string $path, string $name)
    {
        $this->poll = $poll;
        $this->filesystem = $filesystem;
        $this->loop = $loop;
        $this->uvLoop = $loop->getUvLoop();
        $this->path = $path;
        $this->name = $name;
    }

    public function ls(): Observable
    {
        $this->activate();
        $subject = new Subject();
        uv_fs_scandir($this->uvLoop, $this->path . DIRECTORY_SEPARATOR . $this->name, function (array $contents) use ($subject): void {
            $promises = [];
            $nodes = 0;
            $nodeCount = count($contents);
            foreach ($contents as $node) {
                $promises[] =$this->filesystem->detect($this->path . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . $node)->then(function (Node\NodeInterface $node) use ($subject, &$nodes, $nodeCount) {
                    $subject->onNext($node);
                    $nodes++;

                    if ($nodes === $nodeCount) {
                        $subject->onCompleted();
                    }
                }, function (\Throwable $throwable) use ($subject, &$promises) {
                    $subject->onError($throwable);
                    foreach ($promises as $promise) {
                        if ($promise instanceof CancellablePromiseInterface) {
                            $promise->cancel();
                        }
                    }
                });
            }

            $this->deactivate();
        });


        return $subject;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function name(): string
    {
        return $this->name;
    }

    protected function uvLoop()
    {
        return $this->uvLoop;
    }

    protected function activate(): void
    {
        $this->poll->activate();
    }

    protected function deactivate(): void
    {
        $this->poll->deactivate();
    }
}
