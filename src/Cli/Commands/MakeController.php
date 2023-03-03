<?php

namespace Baubyte\Cli\Commands;

use Baubyte\App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeController extends Command {
    /**
     * @inheritDoc
     */
    protected static $defaultName = "make:controller";
    /**
     * @inheritDoc
     */
    protected static $defaultDescription = "Crear un nuevo Controlador.";

    /**
     * @inheritDoc
    */
    protected function configure() {
        $this
            ->addArgument("name", InputArgument::REQUIRED, "Nombre del Controlador")
            ->addOption("suffix", "s", InputOption::VALUE_OPTIONAL, "Agregar el sufijo Controller", "Controller");
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $name = $input->getArgument("name");
        $suffix = $input->getOption("suffix");
        $dir = "";
        $appControllers = "app".DIRECTORY_SEPARATOR."Controllers".DIRECTORY_SEPARATOR;
        $nameSpace = "App\Controllers";

        $directories = explode("/", $name);
        if (count($directories) > 1) {
            $name = array_pop($directories);
            $nameSpace = $nameSpace."\\".ucwords(strtolower(implode("\\", $directories)), "\\");
            $dir = ucwords(strtolower(implode("/", $directories)), "/");
            $dir = str_replace("/", DIRECTORY_SEPARATOR, $dir).DIRECTORY_SEPARATOR;
            @mkdir(App::$root.DIRECTORY_SEPARATOR.$appControllers.$dir, recursive: true);
        }

        $template = str_replace("ControllerName", $name.$suffix, template("controller"));

        $template = str_replace("App\Controllers", $nameSpace, $template);

        file_put_contents(App::$root.DIRECTORY_SEPARATOR."{$appControllers}{$dir}{$name}{$suffix}.php", $template);
        $output->writeln("<info>Controlador Creado => {$name}{$suffix}</info> <comment>[{$appControllers}{$dir}{$name}{$suffix}.php]</comment>");

        return Command::SUCCESS;
    }
}
