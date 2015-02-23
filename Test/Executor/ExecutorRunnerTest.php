<?php

class ExecutorRunnerTest extends PHPUnit_Framework_TestCase
{
    public function testIfAllEventsAreReturned()
    {
        $runner    = new \Velikonja\LabbyBundle\Executor\ExecutorRunner(array());
        $events    = $runner->getSubscribedEvents();
        $allEvents = \Velikonja\LabbyBundle\Events::all();

        $this->assertEquals(
            count($allEvents),
            count($events)
        );
    }

    public function testIfContainsPostSyncEventMethodName()
    {
        $runner = new \Velikonja\LabbyBundle\Executor\ExecutorRunner(array());
        $events = $runner->getSubscribedEvents();

        $this->assertContains(\Velikonja\LabbyBundle\Events::POST_SYNC, $events);
    }

    public function testIfContainsPostSyncEventName()
    {
        $runner = new \Velikonja\LabbyBundle\Executor\ExecutorRunner(array());
        $events = $runner->getSubscribedEvents();

        $this->assertArrayHasKey(\Velikonja\LabbyBundle\Events::POST_SYNC, $events);
    }
}
