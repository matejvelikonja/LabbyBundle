<?php

namespace Velikonja\LabbyBundle\Database;

use Symfony\Component\Console\Output\OutputInterface;

class SyncerDb
{
    /**
     * @var MySqlImporter
     */
    private $importer;

    public function __construct(array $config, MySqlImporter $importer)
    {
        $this->importer = $importer;
    }

    public function sync(OutputInterface $output)
    {
        //TODO: connect and dump sql on remote
        //TODO: copy file locally
        //TODO: import sql
//        $this->importer->import($file);

        throw new \Exception('Not implemented ' . __CLASS__ );
    }
}