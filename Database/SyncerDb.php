<?php

namespace Velikonja\LabbyBundle\Database;

use Symfony\Component\Console\Output\OutputInterface;
use Velikonja\LabbyBundle\Command\DumpCommand;
use Velikonja\LabbyBundle\Remote\Ssh;
use Velikonja\LabbyBundle\Remote\Scp;
use Velikonja\LabbyBundle\Util\ZipArchive;

class SyncerDb
{
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

    /**
     * @var ZipArchive
     */
    private $zip;

    /**
     * @param MySqlImporter $importer
     * @param Ssh           $ssh
     * @param Scp           $scp
     * @param ZipArchive    $zip
     */
    public function __construct(MySqlImporter $importer, Ssh $ssh, Scp $scp, ZipArchive $zip)
    {
        $this->importer = $importer;
        $this->ssh      = $ssh;
        $this->scp      = $scp;
        $this->zip      = $zip;
    }

    /**
     * @param OutputInterface $output
     *
     * @throws DatabaseException
     * @throws \Exception
     */
    public function sync(OutputInterface $output)
    {
        $remoteFile = 'dump-remote.zip';
        $localFile  = 'dump-local.zip';

        $this->ssh->execSf(
            DumpCommand::COMMAND_NAME,
            array(
                $remoteFile,
                '--compress',
            ),
            $output
        );

        $this->scp->copyFile($remoteFile, $localFile, $output);

        $dump = $this->zip->unzip($localFile);

        $this->importer->import($dump);

        // cleanup
        unlink($localFile);
        unlink($dump);

        //TODO: remove remote file
    }
}