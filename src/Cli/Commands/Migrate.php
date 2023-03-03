<?php

namespace Baubyte\Cli\Commands;

use Baubyte\Database\Migrations\Migrator;
use PDOException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migrate extends Command {
    /**
     * @inheritDoc
     */
    protected static $defaultName = "migrate";

    /**
     * @inheritDoc
     */
    protected static $defaultDescription = "Ejecutar las Migraciones";

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            app(Migrator::class)->migrate();
            return Command::SUCCESS;
        } catch (PDOException $ex) {
            $output->writeln("<error>No se pudo ejecutar las Migraciones: {$ex->getMessage()} </error>");
            $output->writeln("<error>{$ex->getTraceAsString()}</error>");
            return Command::FAILURE;
        }
    }
}
