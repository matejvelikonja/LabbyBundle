<?php

namespace Velikonja\LabbyBundle\Executor;

use Symfony\Component\Console\Output\OutputInterface;

interface ExecutorInterface
{
    /**
     * @param string               $command
     * @param OutputInterface|null $output
     *
     * @return int
     */
    public function execute($command, OutputInterface $output = null);

    /**
     * @return string
     */
    public function getName();
}
