<?php

namespace Velikonja\LabbyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportCommand
 *
 * @package Velikonja\LabbyBundle\Command
 * @author  Matej Velikonja <matej@velikonja.si>
 */
class ImportCommand extends ContainerAwareCommand
{
    const COMMAND_NAME    = 'labby:database:import';

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Import SQL dump to local database.')
            ->addArgument('file', InputArgument::REQUIRED, 'Location of SQL dump (compressed or uncompressed).');

        /**
         * TODO: prevent running this command on certain environment (like production).
         */
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = $input->getArgument('file');
        $tempFile = false;
        $zip      = $this->getContainer()->get('velikonja_labby.util.zip_archive');

        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'File `%s` does not exists.',
                    $filePath
                )
            );
        }

        if ($zip->isZipped($filePath)) {
            $filePath = $zip->unzip($filePath);
            $tempFile = true;
        }

        $importer = $this->getContainer()->get('velikonja_labby.service.db.importer');

        $importer->import($filePath);

        // cleanup if we unzip archive
        if ($tempFile) {
            unlink($filePath);
        }

        $output->writeln('<info>Database successfully imported!</info>');
    }
}
