<?php

namespace Baubyte\Cli\Commands;

use Baubyte\Database\Migrations\Migrator;
use PDOException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateRefresh extends Command {
    /**
     * @inheritDoc
     */
    protected static $defaultName = "migrate:refresh";

    /**
     * @inheritDoc
     */
    protected static $defaultDescription = "Revertir las Migraciones y volver a ejecutarlas";

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            app(Migrator::class)->rollback();
            app(Migrator::class)->migrate();
            return Command::SUCCESS;
        } catch (PDOException $ex) {
            $output->writeln("<error>No se pudo refrescar las Migraciones: {$ex->getMessage()} </error>");
            $output->writeln("<error>{$ex->getTraceAsString()}</error>");
            return Command::FAILURE;
        }
    }
}
