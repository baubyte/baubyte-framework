<?php

namespace Baubyte\Database\Migrations;

use Baubyte\Database\Drivers\DatabaseDriver;
use Symfony\Component\Console\Output\ConsoleOutput;

class Migrator {
    /**
     * ConsoleOutput
     *
     * @var ConsoleOutput
     */
    private ConsoleOutput $output;
    /**
     * Migration directory
     *
     * @var string
     */
    private string $migrationsDirectory;

    /**
     * Templates Migration directory
     *
     * @var string
     */
    private string $templatesDirectory;

    /**
     * Driver Database
     *
     * @var DatabaseDriver
     */
    private DatabaseDriver $driver;

    /**
     * Show log progress
     *
     * @var boolean
     */
    private bool $logProgress;

    /**
     * Build migrator.
     * @param string $migrationsDirectory
     * @return self
     */
    public function __construct($migrationsDirectory, $templatesDirectory, DatabaseDriver $driver, $logProgress = true) {
        $this->migrationsDirectory = $migrationsDirectory;
        $this->templatesDirectory = $templatesDirectory;
        $this->driver = $driver;
        $this->logProgress = $logProgress;
        $this->output = new ConsoleOutput();
    }

    /**
     * Logs for migrations
     *
     * @param string $message
     * @return void
     */
    private function log(string $message, $adicionalComment = null) {
        if ($this->logProgress) {
            $adicionalComment = (is_null($adicionalComment)) ? "" : "<comment>{$adicionalComment}</comment>";
            $this->output->writeln("<info>{$message}</info> {$adicionalComment}");
        }
    }

    /**
     * Create Table for Migrations
     *
     * @return void
     */
    private function createMigrationsTableIfNotExists() {
        $this->driver->statement("CREATE TABLE IF NOT EXISTS migrations (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(256))");
    }

    /**
     * Run Migration
     *
     * @return void
     */
    public function migrate() {
        $this->createMigrationsTableIfNotExists();
        $migrated = $this->driver->statement("SELECT * FROM migrations");
        $migrations = glob("$this->migrationsDirectory".DIRECTORY_SEPARATOR."*.php");

        if (count($migrated) >= count($migrations)) {
            $this->log("<comment>Nada que migrar</comment>");
            return;
        }

        foreach (array_slice($migrations, count($migrated)) as $file) {
            $migration = require $file;
            $migration->up();
            $name = basename($file);
            $this->driver->statement("INSERT INTO migrations (name) VALUES (?)", [$name]);
            $this->log("<info>Migrado => {$name} </info>");
        }
    }

     /**
      * Reverse migrations.
      *
      * @param integer|null $steps Number of migrations to reverse, all by default.
      * @return void
      */
    public function rollback(?int $steps = null) {
        $this->createMigrationsTableIfNotExists();
        $migrated = $this->driver->statement("SELECT * FROM migrations");

        $pending = count($migrated);

        if ($pending == 0) {
            $this->log("Nada que revertir");
            return;
        }

        if (is_null($steps) || $steps > $pending) {
            $steps = $pending;
        }

        $migrations = array_slice(array_reverse(glob("$this->migrationsDirectory".DIRECTORY_SEPARATOR."*.php")), -$pending);

        foreach ($migrations as $file) {
            $migration = require $file;
            $migration->down();
            $name = basename($file);
            $this->driver->statement("DELETE FROM migrations WHERE name = ?", [$name]);
            $this->log("Revertido => " . substr($name, 18) . PHP_EOL);
            if (--$steps == 0) {
                break;
            }
        }
    }

    /**
     * Create new migration.
     *
     * @param string $migrationName
     * @return string file name of the migration
     */
    public function make(string $migrationName): string {
        $migrationName = snake_case($migrationName);
        $date = date("Y_m_d");
        $id = 0;
        foreach (glob("$this->migrationsDirectory".DIRECTORY_SEPARATOR."*.php") as $file) {
            if (str_starts_with(basename($file), $date)) {
                $id++;
            }
        }
        $template = file_get_contents("$this->templatesDirectory".DIRECTORY_SEPARATOR."migration.php");

        if (preg_match('/create_.*_table/', $migrationName)) {
            $table = preg_replace_callback("/create_(.*)_table/", fn ($match) => $match[1], $migrationName);
            $template = str_replace('$UP', "CREATE TABLE $table (id INT AUTO_INCREMENT PRIMARY KEY)", $template);
            $template = str_replace('$DOWN', "DROP TABLE $table", $template);
        } elseif (preg_match('/.*(from|to)_(.*)_table/', $migrationName)) {
            $table = preg_replace_callback('/.*(from|to)_(.*)_table/', fn ($match) => $match[2], $migrationName);
            $template = preg_replace('/\$UP|\$DOWN/', "ALTER TABLE $table", $template);
        } else {
            $template = preg_replace_callback('/DB::statement.*/', fn ($match) => "// {$match[0]}", $template);
        }
        $fileName = sprintf("%s_%06d_%s", $date, $id, $migrationName);
        file_put_contents("$this->migrationsDirectory".DIRECTORY_SEPARATOR."$fileName.php", $template);

        $this->log("Migración Creada => {$fileName}", "[database".DIRECTORY_SEPARATOR."migrations".DIRECTORY_SEPARATOR."{$fileName}.php]");

        return $fileName.".php";
    }
}
