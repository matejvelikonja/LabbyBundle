<?php

namespace Velikonja\LabbyBundle\Test\Executor;

use Velikonja\LabbyBundle\Events;
use Velikonja\LabbyBundle\Executor\ExecutorRunner;

class ExecutorRunnerTest extends \PHPUnit_Framework_TestCase
{
    public function testIfAllEventsAreReturned()
    {
        $runner    = new ExecutorRunner(array());
        $events    = $runner->getSubscribedEvents();
        $allEvents = Events::all();

        $this->assertEquals(
            count($allEvents),
            count($events)
        );
    }

    public function testIfContainsPostSyncEventMethodName()
    {
        $runner = new ExecutorRunner(array());
        $events = $runner->getSubscribedEvents();

        $this->assertContains(Events::POST_SYNC, $events);
    }

    public function testIfContainsPostSyncEventName()
    {
        $runner = new ExecutorRunner(array());
        $events = $runner->getSubscribedEvents();

        $this->assertArrayHasKey(Events::POST_SYNC, $events);
    }
}
