<?php

namespace Velikonja\LabbyBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class SyncCommand extends BaseCommand
{
    const COMMAND_NAME = 'labby:sync';

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setRoles(array(self::ROLE_LOCAL))
            ->setName(self::COMMAND_NAME)
            ->setDescription('Run synchronization of filesystem maps and DB.');
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

        $stopwatch->start('sync');
        $syncer->sync($output);
        $event = $stopwatch->stop('sync');

        $output->writeln('');
        $output->writeln(
            sprintf(
                '<info>Finished in %.2f seconds!</info>',
                $event->getDuration() / 1000
            )
        );
    }
}
