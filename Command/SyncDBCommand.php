<?php

namespace Velikonja\LabbyBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncDBCommand extends BaseCommand
{
    const COMMAND_NAME = 'labby:sync:db';

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Run synchronization of database.')
            ->setRoles(array(self::ROLE_LOCAL));
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $syncer = $this->getContainer()->get('velikonja_labby.service.syncer');

        $syncer->syncDb($output);

        $output->writeln('<info>Finished!</info>');
    }
}
