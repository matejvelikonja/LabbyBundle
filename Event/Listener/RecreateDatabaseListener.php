<?php

namespace Velikonja\LabbyBundle\Event\Listener;

use Velikonja\LabbyBundle\Database\CreatorDB;
use Velikonja\LabbyBundle\Event\SyncEvent;

class RecreateDatabaseListener
{
    /**
     * @var CreatorDB
     */
    private $creator;

    /**
     * @param CreatorDB $creator
     */
    public function __construct(CreatorDB $creator)
    {
        $this->creator = $creator;
    }

    /**
     * Changes password for all users.
     *
     * @param SyncEvent $event
     */
    public function onPreSyncDb(SyncEvent $event)
    {
        $this->creator->create($event->getOutput());
    }
}
