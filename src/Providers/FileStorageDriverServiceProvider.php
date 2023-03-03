<?php

namespace Baubyte\Providers;

use Baubyte\App;
use Baubyte\Storage\Drivers\DiskFileStorage;
use Baubyte\Storage\Drivers\FileStorageDriver;

class FileStorageDriverServiceProvider {
    public function registerServices() {
        match (config("storage.driver", "disk")) {
            "disk" => singleton(
                FileStorageDriver::class,
                fn () => new DiskFileStorage(
                    App::$root.DIRECTORY_SEPARATOR."storage",
                    "storage",
                    config("app.url")
                )
            ),
        };
    }
}
