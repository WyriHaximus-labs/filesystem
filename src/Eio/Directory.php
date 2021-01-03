<?php

namespace React\Filesystem\Eio;

use React\EventLoop\ExtUvLoop;
use React\Filesystem\FilesystemInterface;
use React\Filesystem\Node\FileInterface;
use React\Filesystem\Stat;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use RuntimeException;
use React\EventLoop\LoopInterface;
use React\Filesystem\Node;
use Rx\Observable;
use Rx\Subject\Subject;
use UV;
use function React\Promise\all;
use function React\Promise\resolve;

final class Directory implements Node\DirectoryInterface
{
    use StatTrait;

    private Poll $poll;
    private FilesystemInterface $filesystem;
    private string $path;
    private string $name;
    private int $workInProgress = 0;

    public function __construct(Poll $poll, FilesystemInterface $filesystem, string $path, string $name)
    {
        $this->poll = $poll;
        $this->filesystem = $filesystem;
        $this->path = $path;
        $this->name = $name;
    }

    public function ls(): Observable
    {
        $this->activate();
        $subject = new Subject();
        eio_readdir($this->path . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR, EIO_READDIR_STAT_ORDER, false, function ($_, array $contents) use ($subject): void {
            $list = [];
            foreach ($contents['dents'] as $node) {
                switch ($node['type'] ?? null) {
                    case EIO_DT_DIR:
                        $subject->onNext(new Directory($this->poll, $this->filesystem, $this->path . DIRECTORY_SEPARATOR . $this->name, $node['name']));
                        break;
                    case EIO_DT_REG :
                        $subject->onNext(new File($this->poll, $this->path . DIRECTORY_SEPARATOR . $this->name, $node['name']));
                        break;
                    default:
                        $list[] = $this->filesystem->detect($this->path . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . $node['name']);
                        break;
                }
            }
            $subject->onCompleted();
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

    protected function activate(): void
    {
        $this->poll->activate();
    }

    protected function deactivate(): void
    {
        $this->poll->deactivate();
    }
}
