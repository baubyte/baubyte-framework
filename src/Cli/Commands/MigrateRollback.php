<?php

namespace Baubyte\Cli\Commands;

use Baubyte\Database\Migrations\Migrator;
use PDOException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateRollback extends Command {
    /**
     * @inheritDoc
     */
    protected static $defaultName = "migrate:rollback";

    /**
     * @inheritDoc
     */
    protected static $defaultDescription = "Revertir las Migraciones";

    /**
     * @inheritDoc
     */
    protected function configure() {
        $this->addArgument("steps", InputArgument::OPTIONAL, "Cantidad de Migraciones a revertir. Todo por defecto.");
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            $steps = $input->getArgument("steps") ?? null;
            app(Migrator::class)->rollback($steps);
            return Command::SUCCESS;
        } catch (PDOException $ex) {
            $output->writeln("<error>No se pudo revertir las Migraciones: {$ex->getMessage()} </error>");
            $output->writeln("<error>{$ex->getTraceAsString()}</error>");
            return Command::FAILURE;
        }
    }
}
