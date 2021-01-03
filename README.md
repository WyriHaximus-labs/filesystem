# Filesystem

[![CI status](https://github.com/reactphp/filesystem/workflows/CI/badge.svg)](https://github.com/reactphp/filesystem/actions)

Event-driven filesystem implementation for [ReactPHP](https://reactphp.org/).

**Table of contents**

* [Quickstart example](#quickstart-example)
* [Filesystem Usage](#filesystem-usage)
  * [File Node Usage](#file-node-usage)
  * [Directory Node Usage](#directory-node-usage)
* [Install](#install)
* [Tests](#tests)
* [License](#license)

## Quickstart example

Once [installed](#install), you can use the following code to quickly 
read a file:

```php
$loop = \React\EventLoop\Factory::create();

\React\Filesystem\Factory::create($loop)->file(__FILE__)->getContents()->then(static function (string $contents): void {
    echo $contents;
})->done();

$loop->run();
```

See also the [examples](examples/).

## Filesystem Usage

The filesystem object is created through the factory just like you create the event loop. The factory will create the 
best performance filesystem for you depending on which extensions you have available and which loop you pass in:

```php
$filesystem = \React\Filesystem\Factory::create($loop);
```

The following different filesystem adapters are build in:

* `Uv` - only used when the [`ExtUvLoop`](https://reactphp.org/event-loop/#extuvloop) is pass into the factory.
* `Eio` - only used when `ext-eio` is available.

The filesystem comes with factory 3 methods to create an object representing a node on the filesystem:

* `file` - creates an object representing a [File Node](#file-node-usage)
* `directory` - creates an object representing a [Directory Node](#directory-node-usage)
* `detect` - creates an object representing one of the node types listed above, depending on what it detects

### File Node Usage

There are a few ways to get a file node. The most common one is using the `detect` method on the filesystem:

```php
$file = $filesystem->detect(__FILE__);
```

Another way is when you get it passed in the return list of a `DirectoryInterface::ls` call.

#### Reading the contents of a file

File nodes offer a method behaving exactly like `file_get_contents`, namely `getContents`, to read all the contents 
from the represented file into a string.

```php
$file->getContents()->then(static function (string $contents): void {
    echo $contents; // Echo's the contents on the given file
});
```

### Directory Node Usage

There are a few ways to get a directory node. The most common one is using the `detect` method on the filesystem:

```php
$directory = $filesystem->detect(__DIR__);
```

Another way is when you get it passed in the return list of a `DirectoryInterface::ls` call.

#### Listing directory contents

The `ls` method lists the contents of a directory and returns an `Observable` with nodes:

```php
$directory->ls()->subscribe(static function (\React\Filesystem\Node\NodeInterface $node) {
    echo $node->name(), ': ', get_class($node), PHP_EOL;
});
```

## Install

The recommended way to install this library is [through Composer](https://getcomposer.org).
[New to Composer?](https://getcomposer.org/doc/00-intro.md)

This project follows [SemVer](https://semver.org/). This will install the latest supported version:

```bash
$ composer require react/filesystem:^0.2
```

This project aims to run on any platform and thus does not require any PHP
extensions and supports running on legacy PHP 7.4 through current PHP 8+ and
HHVM.
It's *highly recommended to use PHP 8+* for this project.

## Tests

To run the test suite, you first need to clone this repo and then install all
dependencies [through Composer](https://getcomposer.org):

```bash
$ composer install
```

To run the test suite, go to the project root and run:

```bash
$ php vendor/bin/phpunit
```

## License

MIT, see [LICENSE file](LICENSE).