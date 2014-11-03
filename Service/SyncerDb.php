<?php

namespace Velikonja\LabbyBundle\Service;

use Symfony\Component\Console\Output\OutputInterface;

class SyncerDb
{

    public function __construct(array $config)
    {
    }

    public function sync(OutputInterface $output)
    {
        throw new \Exception('Not implemented ' . __CLASS__ );
    }
}