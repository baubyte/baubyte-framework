<?php

namespace Baubyte\Providers;

use Baubyte\Database\Drivers\DatabaseDriver;
use Baubyte\Database\Drivers\PdoDriver;

class DatabaseDriverServiceProvider implements ServiceProvider {
    public function registerServices() {
        match (config("database.connection", "mysql")) {
            "mysql", "pgsql" => singleton(DatabaseDriver::class, PdoDriver::class),
        };
    }
}
