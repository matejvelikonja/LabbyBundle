<?php

namespace Velikonja\LabbyBundle\Database;

use Symfony\Component\Console\Output\OutputInterface;
use Velikonja\LabbyBundle\Command\DumpCommand;
use Velikonja\LabbyBundle\Remote\Ssh;
use Velikonja\LabbyBundle\Remote\Scp;

class SyncerDb
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var MySqlImporter
     */
    private $importer;

    /**
     * @var Ssh
     */
    private $ssh;

    /**
     * @var Scp
     */
    private $scp;

    public function __construct(array $config, MySqlImporter $importer, Ssh $ssh, Scp $scp)
    {
        $this->config   = $config;
        $this->importer = $importer;
        $this->ssh      = $ssh;
        $this->scp      = $scp;
    }

    public function sync(OutputInterface $output)
    {
        $this->ssh->execSf(
            DumpCommand::COMMAND_NAME,
            array(
                'dump-remote.zip',
                '--compress',
            ),
            $output
        );

        $this->scp->copyFile('dump-remote.zip', 'dump-local.zip', $output);

        die(2);

        //TODO: unzip the file

        $this->importer->import('dump-local.zip');

        throw new \Exception('Not implemented ' . __CLASS__ );
    }
}