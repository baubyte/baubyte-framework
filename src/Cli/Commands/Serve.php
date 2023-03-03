<?php

namespace Baubyte\Cli\Commands;

use Baubyte\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Serve extends Command {
    /**
     * @inheritDoc
     */
    protected static $defaultName = "serve";
    /**
     * @inheritDoc
     */
    protected static $defaultDescription = "Ejecutar la aplicación de desarrollo Baubyte.";

    /**
     * @inheritDoc
    */
    protected function configure() {
        $this
            ->addOption("host", null, InputOption::VALUE_OPTIONAL, "Dirección de host", "127.0.0.1")
            ->addOption("port", null, InputOption::VALUE_OPTIONAL, "Puerto", "8080");
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $host = $input->getOption("host");
        $port = $input->getOption("port");
        $dir = App::$root.DIRECTORY_SEPARATOR."public";

        $output->writeln("<info>Inicio del servidor de desarrollo en {$host}:{$port}</info>");
        shell_exec("php -S {$host}:{$port} {$dir}".DIRECTORY_SEPARATOR."index.php");

        return Command::SUCCESS;
    }
}
