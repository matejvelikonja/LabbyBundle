<?php

namespace Velikonja\LabbyBundle\Database;

use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Velikonja\LabbyBundle\Command\DumpCommand;
use Velikonja\LabbyBundle\Remote\Ssh;
use Velikonja\LabbyBundle\Remote\Scp;
use Velikonja\LabbyBundle\Util\ZipArchive;

class SyncerDb
{
    /**
     * @var ImporterInterface
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
     * @var string
     */
    private $tmpDir;

    /**
     * @param ImporterInterface $importer
     * @param Ssh               $ssh
     * @param Scp               $scp
     * @param ZipArchive        $zip
     *
     * @throws \Exception
     */
    public function __construct(
        ImporterInterface $importer,
        Ssh $ssh,
        Scp $scp,
        ZipArchive $zip
    ) {
        $this->importer = $importer;
        $this->ssh      = $ssh;
        $this->scp      = $scp;
        $this->zip      = $zip;
        $this->tmpDir   = sys_get_temp_dir();

        if (!is_writable($this->tmpDir)) {
            throw new \Exception(
                sprintf('Temporary directory `%s` is unreadable.', $this->tmpDir)
            );
        }
    }

    /**
     * @param null|OutputInterface $output
     *
     * @throws DatabaseException
     * @throws \Exception
     */
    public function sync(OutputInterface $output = null)
    {
        $output     = $output ?: new NullOutput();
        $remoteFile = 'dump-remote.zip'; //TODO: improve location and name of remote file
        $localFile  = $this->tmpDir . '/labby_' . uniqid() . '.zip';

        $this->write('Executing remote dump...', $output);

        $this->ssh->execSf(
            DumpCommand::COMMAND_NAME,
            array(
                $remoteFile,
                '--compress',
            ),
            $this->getOutputCallback($output, 1)
        );

        $this->write('Copying...', $output);
        $this->scp->copyFile($remoteFile, $localFile, $output);
        $this->write(
            sprintf(
                "Remote file `%s` copied locally to `%s`.",
                $remoteFile,
                $localFile
            ),
            $output,
            1
        );

        $dumpFile = $this->zip->unzip($localFile);
        $this->write(
            sprintf(
                "File `%s` unzipped to `%s`.",
                $localFile,
                $dumpFile
            ),
            $output,
            1
        );

        //TODO: recreate database

        $this->write('Importing...', $output);
        $this->importer->import($dumpFile, $this->getOutputCallback($output, 1));
        $this->write(
            sprintf(
                "File `%s` imported.",
                $dumpFile
            ),
            $output,
            1
        );

        $this->write('Cleaning up...', $output);

        unlink($localFile);
        $this->write(
            sprintf(
                "File `%s` removed.",
                $localFile
            ),
            $output,
            1
        );

        unlink($dumpFile);
        $this->write(
            sprintf(
                "File `%s` removed.",
                $dumpFile
            ),
            $output,
            1
        );

        //TODO: remove remote file
    }

    /**
     * @param OutputInterface $output
     * @param int             $level
     *
     * @return null|\Closure
     */
    private function getOutputCallback(OutputInterface $output, $level = 0)
    {
        if ($output->getVerbosity() < OutputInterface::VERBOSITY_VERBOSE) {
            return null;
        }

        return function ($type, $buffer) use ($output, $level) {
            $output->write(str_repeat(' ', $level * 2));
            if (Process::ERR === $type) {
                $output->writeln(sprintf('<error>%s</error>', $buffer));
            } else {
                $output->writeln(sprintf('%s', $buffer));
            }
        };
    }

    /**
     * @param string          $string
     * @param OutputInterface $output
     * @param int             $level
     */
    private function write($string, OutputInterface $output, $level = 0)
    {
        $info = str_repeat(' ', $level * 2) . $string;
        $output->writeln(sprintf('<comment>%s</comment>', $info));
    }
}
