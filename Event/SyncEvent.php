<?php

namespace Velikonja\LabbyBundle\Event;

use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;

class SyncEvent extends Event
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output = null)
    {
        if (! $output) {
            $output = new NullOutput();
        }

        $this->output = $output;
    }

    public function getOutput()
    {
        return $this->output;
    }
}
