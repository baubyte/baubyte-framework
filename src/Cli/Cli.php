<?php

namespace Baubyte\Cli;

use Baubyte\App;
use Baubyte\Cli\Commands\MakeController;
use Baubyte\Cli\Commands\MakeMiddleware;
use Baubyte\Cli\Commands\MakeMigration;
use Baubyte\Cli\Commands\MakeModel;
use Baubyte\Cli\Commands\Migrate;
use Baubyte\Cli\Commands\MigrateRefresh;
use Baubyte\Cli\Commands\MigrateRollback;
use Baubyte\Cli\Commands\Serve;
use Baubyte\Cli\Commands\StorageLink;
use Baubyte\Config\Config;
use Baubyte\Database\Drivers\DatabaseDriver;
use Baubyte\Database\Migrations\Migrator;
use Dotenv\Dotenv;
use Symfony\Component\Console\Application;

class Cli {
    /**
     * Database Drivers
     *
     * @var \Baubyte\Database\Drivers\DatabaseDriver
     */
    public DatabaseDriver $database;

    public static function bootstrap(string $root): self {
        App::$root = $root;
        $cli =  new self();
        Dotenv::createImmutable($root)->load();
        Config::load($root .DIRECTORY_SEPARATOR."config");
        foreach (config("providers.cli") as $providers) {
            (new $providers())->registerServices();
        }
        return $cli->setUpDatabaseConnections()
                   ->setUpMigrator();
    }

    /**
     * Open database connections or other connections.
     *
     * @return self
     */
    protected function setUpDatabaseConnections(): self {
        $this->database = app(DatabaseDriver::class);
        $this->database->connect(
            config("database.connection"),
            config("database.host"),
            config("database.port"),
            config("database.database"),
            config("database.username"),
            config("database.password")
        );
        return $this;
    }

    /**
     * Set up Migrator.
     *
     * @return self
     */
    protected function setUpMigrator(): self {
        singleton(
            Migrator::class,
            fn () => new Migrator(
                App::$root.DIRECTORY_SEPARATOR."database".DIRECTORY_SEPARATOR."migrations",
                resourcesDirectory() .DIRECTORY_SEPARATOR."templates",
                app(DatabaseDriver::class)
            )
        );
        return $this;
    }

    /**
     * Run CLI app.
     *
     * @return void
     */
    public function run() {
        $cli = new Application("Baubyte");

        $cli->addCommands([
            new MakeController(),
            new MakeMigration(),
            new MakeModel(),
            new MakeMiddleware(),
            new Migrate(),
            new MigrateRollback(),
            new MigrateRefresh(),
            new Serve(),
            new StorageLink(),
        ]);
        $cli->run();
    }
}
