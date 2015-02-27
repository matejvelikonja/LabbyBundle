<?php

namespace Velikonja\LabbyBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DumpCommand
 *
 * @package Velikonja\LabbyBundle\Command
 * @author  Matej Velikonja <matej@velikonja.si>
 */
class DumpCommand extends BaseCommand
{
    const COMMAND_NAME    = 'labby:database:dump';
    const ARG_CONFIG_PATH = 'config-path';

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Dump local database.')
            ->addArgument('file', InputArgument::OPTIONAL, 'File path to write to.')
            ->addOption(
                'compress',
                'c',
                InputOption::VALUE_NONE,
                'Compress file. Works only if file argument is given.'
            )
            ->setRoles(array(self::ROLE_REMOTE));
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
        $compress = $input->getOption('compress');
        $dumper   = $this->getContainer()->get('velikonja_labby.service.db.dumper');
        $zip      = $this->getContainer()->get('velikonja_labby.util.zip_archive');

        $dump = $dumper->dump($output);

        if ($filePath) {
            if ($compress) {
                $zip->zip($filePath, $dump);
            } else {
                file_put_contents($filePath, $dump);
            }
            $output->writeln(sprintf('<info>Database successfully dumped to `%s`!</info>', realpath($filePath)));
        } else {
            $output->writeln($dump);
        }

    }
}
