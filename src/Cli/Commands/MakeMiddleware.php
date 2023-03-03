<?php

namespace Baubyte\Cli\Commands;

use Baubyte\App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeMiddleware extends Command {
    /**
     * @inheritDoc
     */
    protected static $defaultName = "make:middleware";
    /**
     * @inheritDoc
     */
    protected static $defaultDescription = "Crear un nuevo Middleware.";

    /**
     * @inheritDoc
    */
    protected function configure() {
        $this
            ->addArgument("name", InputArgument::REQUIRED, "Nombre del Middleware")
            ->addOption("suffix", "s", InputOption::VALUE_OPTIONAL, "Agregar el sufijo Middleware", "Middleware");
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $name = $input->getArgument("name");
        $suffix = $input->getOption("suffix");
        $dir = "";
        $appMiddleware = "app".DIRECTORY_SEPARATOR."Middlewares".DIRECTORY_SEPARATOR;
        $nameSpace = "App\Middlewares";

        $directories = explode("/", $name);
        if (count($directories) > 1) {
            $name = array_pop($directories);
            $nameSpace = $nameSpace."\\".ucwords(strtolower(implode("\\", $directories)), "\\");
            $dir = ucwords(strtolower(implode("/", $directories)), "/");
            $dir = str_replace("/", DIRECTORY_SEPARATOR, $dir).DIRECTORY_SEPARATOR;
            @mkdir(App::$root.DIRECTORY_SEPARATOR.$appMiddleware.$dir, recursive: true);
        }

        $template = str_replace("MiddlewareName", $name.$suffix, template("middleware"));

        $template = str_replace("App\Middlewares", $nameSpace, $template);

        file_put_contents(App::$root.DIRECTORY_SEPARATOR."{$appMiddleware}{$dir}{$name}{$suffix}.php", $template);
        $output->writeln("<info>Middleware Creado => {$name}{$suffix}</info> <comment>[{$appMiddleware}{$dir}{$name}{$suffix}.php]</comment>");

        return Command::SUCCESS;
    }
}
