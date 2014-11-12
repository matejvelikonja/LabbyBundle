<?php

namespace Velikonja\LabbyBundle\Test;

class EventsTest extends \PHPUnit_Framework_TestCase
{
    public function testIfEventsCannotBeInitialized()
    {
        $reflection = new \ReflectionClass('Velikonja\LabbyBundle\Events');

        $this->assertTrue(
            $reflection->getConstructor()->isPrivate()
        );
    }
}
