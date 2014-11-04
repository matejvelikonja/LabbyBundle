<?php

namespace Velikonja\LabbyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DumpCommand
 *
 * @package Velikonja\LabbyBundle\Command
 * @author  Matej Velikonja <matej@velikonja.si>
 */
class DumpCommand extends ContainerAwareCommand
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
            ->addArgument('file', InputArgument::OPTIONAL, 'File path to write to.');
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
        $dumper   = $this->getContainer()->get('velikonja_labby.service.dumper');

        $dump = $dumper->dump($output);

        if ($filePath) {
            file_put_contents($filePath, $dump);
        }

        $output->writeln($dump);

        $output->writeln('<info>Database successfully dumped!</info>');
    }
}
