<?php

namespace Velikonja\LabbyBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class SyncDBCommand extends BaseCommand
{
    const COMMAND_NAME = 'labby:sync:db';

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setRoles(array(self::ROLE_LOCAL))
            ->setName(self::COMMAND_NAME)
            ->setDescription('Run synchronization of database.');
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
        $syncer    = $this->getContainer()->get('velikonja_labby.service.syncer');
        $stopwatch = new Stopwatch();

        $stopwatch->start('sync_db');
        $syncer->syncDb($output);
        $event = $stopwatch->stop('sync_db');

        $output->writeln('');
        $output->writeln(
            sprintf(
                '<info>Finished in %.2f seconds!</info>',
                $event->getDuration() / 1000
            )
        );
    }
}
