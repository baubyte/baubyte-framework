<?php

namespace Baubyte\Tests\Database;

use Baubyte\Database\Drivers\DatabaseDriver;
use Baubyte\Database\Drivers\PdoDriver;
use Baubyte\Database\Model;
use PDOException;

trait RefreshDatabase {
    protected function setUp(): void {
        if (is_null($this->driver)) {
            $this->driver = singleton(DatabaseDriver::class, PdoDriver::class);
            Model::setDatabaseDriver($this->driver);
            try {
                $this->driver->connect('mysql', 'framework-db', 3306, 'framework_test', 'root', 'admin.root');
            } catch (PDOException $error) {
                $this->markTestSkipped("Can´t connect to test database. {$error}");
            }
        }
    }

    protected function tearDown(): void {
        $this->driver->statement("DROP DATABASE IF EXISTS framework_test");
        $this->driver->statement("CREATE DATABASE framework_test");
    }
}
