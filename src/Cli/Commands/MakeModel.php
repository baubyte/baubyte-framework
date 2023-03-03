<?php

namespace Baubyte\Cli\Commands;

use Baubyte\App;
use Baubyte\Database\Migrations\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeModel extends Command {
    /**
     * @inheritDoc
     */
    protected static $defaultName = "make:model";
    /**
     * @inheritDoc
     */
    protected static $defaultDescription = "Crear un nuevo Modelo.";

    /**
     * @inheritDoc
    */
    protected function configure() {
        $this
            ->addArgument("name", InputArgument::REQUIRED, "Nombre del Modelo")
            ->addOption("migration", "m", InputOption::VALUE_OPTIONAL, "Crear también un archivo de migración", false)
            ->addOption("suffix", "s", InputOption::VALUE_OPTIONAL, "Agregar el sufijo Model", "");
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $name = $input->getArgument("name");
        $migration = $input->getOption("migration");
        $dir = "";
        $appModels= "app".DIRECTORY_SEPARATOR."Models".DIRECTORY_SEPARATOR;
        $nameSpace = "App\Models";

        $directories = explode("/", $name);
        if (count($directories) > 1) {
            $name = array_pop($directories);
            $nameSpace = $nameSpace."\\".ucwords(strtolower(implode("\\", $directories)), "\\");
            $dir = ucwords(strtolower(implode("/", $directories)), "/");
            $dir = str_replace("/", DIRECTORY_SEPARATOR, $dir).DIRECTORY_SEPARATOR;
            @mkdir(App::$root.DIRECTORY_SEPARATOR.$appModels.$dir, recursive: true);
        }

        $template = str_replace("ModelName", $name, template("model"));
        $template = str_replace("App\Models", $nameSpace, $template);
        $template = str_replace("table_name", snake_case("{$name}")."s", $template);

        file_put_contents(App::$root.DIRECTORY_SEPARATOR."{$appModels}{$dir}{$name}.php", $template);

        $output->writeln("<info>Modelo Creado => {$name}</info> <comment>[{$appModels}{$dir}{$name}.php]</comment>");

        if ($migration !== false) {
            app(Migrator::class)->make("create_{$name}s_table");
        }

        return Command::SUCCESS;
    }
}
