<?php

namespace Velikonja\LabbyBundle\Service;

use Symfony\Component\Console\Output\OutputInterface;
use SyncFS\Syncer as SyncerFs;

class Syncer
{
    /**
     * @var SyncerFs
     */
    private $syncerFs;

    /**
     * @var SyncerDb
     */
    private $syncerDb;

    public function __construct(array $config)
    {
        $this->syncerFs = new SyncerFs($config['fs']);

        if (isset($config['db'])) {
            $this->syncerDb = new SyncerDb($config['db']);
        }

    }

    /**
     * @param OutputInterface $output
     */
    public function sync(OutputInterface $output)
    {
        $this->syncDb($output);
        $this->syncFs($output);
    }

    public function syncFs(OutputInterface $output)
    {
        $this->syncerFs->sync($output);

    }

    public function syncDb(OutputInterface $output)
    {
        $this->syncerDb->sync($output);
    }
}