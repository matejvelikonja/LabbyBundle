<?php

namespace Velikonja\LabbyBundle\Service;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Velikonja\LabbyBundle\Database\SyncerDb;
use SyncFS\Syncer as SyncerFs;
use Velikonja\LabbyBundle\Event\SyncEvent;
use Velikonja\LabbyBundle\Events;

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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param SyncerFs                 $syncerFs
     * @param null|SyncerDb            $syncerDb
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        SyncerFs $syncerFs,
        SyncerDb $syncerDb = null,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->syncerFs        = $syncerFs;
        $this->syncerDb        = $syncerDb;
        $this->eventDispatcher = $eventDispatcher;


    }

    /**
     * @param OutputInterface $output
     */
    public function sync(OutputInterface $output)
    {
        $this->eventDispatcher->dispatch(Events::PRE_SYNC, new SyncEvent($output));
        $this->syncDb($output);
        $this->syncFs($output);
        $this->eventDispatcher->dispatch(Events::POST_SYNC, new SyncEvent($output));
    }

    /**
     * @param OutputInterface $output
     */
    public function syncFs(OutputInterface $output)
    {
        $this->eventDispatcher->dispatch(Events::PRE_SYNC_FS, new SyncEvent($output));
        $this->syncerFs->sync($output);
        $this->eventDispatcher->dispatch(Events::POST_SYNC_FS, new SyncEvent($output));
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

        $this->eventDispatcher->dispatch(Events::PRE_SYNC_DB, new SyncEvent($output));
        $this->syncerDb->sync($output);
        $this->eventDispatcher->dispatch(Events::POST_SYNC_DB, new SyncEvent($output));
    }
}
