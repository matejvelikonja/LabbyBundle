<?php

namespace Velikonja\LabbyBundle\Database;

use Symfony\Component\Console\Output\NullOutput;
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
     *
     * @throws \Exception
     */
    public function __construct(MySqlImporter $importer, Ssh $ssh, Scp $scp, ZipArchive $zip)
    {
        $this->importer = $importer;
        $this->ssh      = $ssh;
        $this->scp      = $scp;
        $this->zip      = $zip;
        $this->tmpDir  = sys_get_temp_dir();

        if (! is_writable($this->tmpDir)) {
            throw new \Exception(
                sprintf('Temporary directory `%s` is unreadable.', $this->tmpDir)
            );
        }
    }

    /**
     * @param OutputInterface $output
     *
     * @throws DatabaseException
     * @throws \Exception
     */
    public function sync(OutputInterface $output = null)
    {
        $output     = $output ?: new NullOutput();
        $remoteFile = 'dump-remote.zip'; //TODO: improve location and name of remote file
        $localFile  = $this->tmpDir . '/labby_' . uniqid() . '.zip';

        $output->writeln('Executing remote dump..');

        $this->ssh->execSf(
            DumpCommand::COMMAND_NAME,
            array(
                $remoteFile,
                '--compress',
            ),
            $output
        );

        $output->writeln('Copying...');
        $this->scp->copyFile($remoteFile, $localFile, $output);
        $output->writeln(
            sprintf(
                'Remote file `%s` copied locally to `%s`.',
                $remoteFile,
                $localFile
            )
        );

        $dumpFile = $this->zip->unzip($localFile);
        $output->writeln(
            sprintf(
                'File `%s` unzipped to `%s`.',
                $localFile,
                $dumpFile
            )
        );

        $this->importer->import($dumpFile);
        $output->writeln(
            sprintf(
                'File `%s` imported.',
                $dumpFile
            )
        );

        $output->writeln('Cleaning up...');

        unlink($localFile);
        $output->writeln(sprintf('File `%s` removed.', $localFile));

        unlink($dumpFile);
        $output->writeln(sprintf('File `%s` removed.', $dumpFile));

        //TODO: remove remote file
    }
}