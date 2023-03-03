<?php

namespace Baubyte\Tests\Storage;

use Baubyte\Storage\Drivers\DiskFileStorage;
use PHPUnit\Framework\TestCase;

class DiskFileStorageTest extends TestCase {
    protected $storageDirectory = __DIR__ . "/test-storage";

    protected function removeTestStorageDirectory() {
        if (file_exists($this->storageDirectory)) {
            shell_exec("rm -r '$this->storageDirectory'");
        }
    }

    protected function setUp(): void {
        $this->removeTestStorageDirectory();
    }

    protected function tearDown(): void {
        $this->removeTestStorageDirectory();
    }

    public function files() {
        return [
            ["test.txt", "Hello World"],
            ["test/test.txt", "Hello World"],
            ["test/subdir/longer/dir/test.txt", "Hello World"],
        ];
    }

    /**
     * @dataProvider files
     */
    public function test_stores_single_file_and_creates_parent_directories($file, $content) {
        $appUrl = "localhost";
        $storageUri = "storage";
        $storage = new DiskFileStorage($this->storageDirectory, $storageUri, $appUrl);
        $url = $storage->put($file, $content);
        $path = "{$this->storageDirectory}".DIRECTORY_SEPARATOR."{$file}";

        $this->assertDirectoryExists($this->storageDirectory);
        $this->assertFileExists($path);
        $this->assertEquals($content, file_get_contents($path));
        $this->assertEquals("$appUrl/$storageUri/$file", $url);
    }

    public function test_stores_multiple_files() {
        $f1 = "test.txt";
        $f2 = "f2.txt";
        $f3 = "foo/bar/f3.txt";
        $storage = new DiskFileStorage($this->storageDirectory, "test", "test");

        foreach ([$f1, $f2, $f3] as $f) {
            $storage->put($f, $f);
        }

        foreach ([$f1, $f2, $f3] as $f) {
            $this->assertFileExists("$this->storageDirectory/$f");
            $this->assertEquals($f, file_get_contents("$this->storageDirectory/$f"));
        }
    }
}
