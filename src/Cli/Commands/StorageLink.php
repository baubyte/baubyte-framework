<?php

namespace Baubyte\Cli\Commands;

use Baubyte\App;
use Baubyte\Exceptions\BaubyteException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StorageLink extends Command {
    /**
     * @inheritDoc
     */
    protected static $defaultName = "storage:link";
    /**
     * @inheritDoc
     */
    protected static $defaultDescription = "Crear un enlace simbólico de storage a public.";

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $targetFolder = App::$root.DIRECTORY_SEPARATOR."storage";
        $linkFolder =  App::$root.DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."storage";
        try {
            @symlink($targetFolder, $linkFolder);
            $output->writeln("<info>Enlace Simbólico Creado</info> <comment>[storage => public/storage]</comment>");
            return Command::SUCCESS;
        } catch (BaubyteException $ex) {
            $output->writeln("<error>No se pudo generar el enlace simbólico: {$ex->getMessage()} </error>");
            $output->writeln("<error>{$ex->getTraceAsString()}</error>");
            return Command::FAILURE;
        }
    }
}
