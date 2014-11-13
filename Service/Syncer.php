<?php

namespace Velikonja\LabbyBundle\Service;

use Symfony\Component\Console\Output\OutputInterface;
use Velikonja\LabbyBundle\Database\SyncerDb;
use SyncFS\Syncer as SyncerFs;

class Syncer
{
    /**
     * @var SyncerFs
     */
    private $syncerFs;

    /**
     * @var SyncerDb | null
     */
    private $syncerDb;

    /**
     * @param SyncerFs      $syncerFs
     * @param null|SyncerDb $syncerDb
     */
    public function __construct(SyncerFs $syncerFs, SyncerDb $syncerDb = null)
    {
        $this->syncerFs = $syncerFs;
        $this->syncerDb = $syncerDb;

    }

    /**
     * @param OutputInterface $output
     */
    public function sync(OutputInterface $output)
    {
        $this->syncDb($output);
        $this->syncFs($output);
    }

    /**
     * @param OutputInterface $output
     */
    public function syncFs(OutputInterface $output)
    {
        $this->syncerFs->sync($output);
    }

    /**
     * @param OutputInterface $output
     *
     * @throws \Exception
     */
    public function syncDb(OutputInterface $output)
    {
        if (! $this->syncerDb) {
            throw new \RuntimeException('Syncer DB is not defined.');
        }

        $this->syncerDb->sync($output);
    }
}
