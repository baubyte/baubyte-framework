<?php

namespace Baubyte\Cli\Commands;

use Baubyte\Database\Migrations\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeMigration extends Command {
    /**
     * @inheritDoc
     */
    protected static $defaultName = "make:migration";

    /**
     * @inheritDoc
     */
    protected static $defaultDescription = "Crea una Migración";

    /**
     * @inheritDoc
     */
    protected function configure() {
        $this->addArgument("name", InputArgument::REQUIRED, "Nombre de la Migración");
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $name = $input->getArgument("name");
        app(Migrator::class)->make($name);
        return Command::SUCCESS;
    }
}
