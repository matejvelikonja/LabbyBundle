<?php

namespace Velikonja\LabbyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportCommand
 *
 * @package Velikonja\LabbyBundle\Command
 * @author  Matej Velikonja <matej@velikonja.si>
 */
class ImportCommand extends ContainerAwareCommand
{
    const COMMAND_NAME    = 'labby:database:import';
    const ARG_CONFIG_PATH = 'config-path';

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Import SQL dump to local database.')
            ->addArgument('file', InputArgument::REQUIRED, 'Location of SQL dump (compressed or uncompressed).');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = $input->getArgument('file');
        $tempFile = false;

        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'File `%s` does not exists.',
                    $filePath
                )
            );
        }

        if ($this->isZipped($filePath)) {
            $filePath = $this->unzip($filePath);
            $tempFile = true;
        }

        $importer = $this->getContainer()->get('velikonja_labby.service.importer');

        $importer->import($filePath);

        // cleanup if we unzip archive
        if ($tempFile) {
            unlink($filePath);
        }

        $output->writeln('<info>Database successfully imported!</info>');
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    private function isZipped($filePath)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $type  = finfo_file($finfo, $filePath);

        finfo_close($finfo);

        return 'application/zip' === $type;
    }

    /**
     * @param string $filePath
     *
     * @return string
     */
    private function unzip($filePath)
    {
        $compressedFileName = 'dump.sql';
        $tmpDir             = sys_get_temp_dir();
        $tmpFile            = $tmpDir . '/labby_' . uniqid() . '.sql';

        $zip = new \ZipArchive();
        $zip->open($filePath);

        if (false === $zip->locateName($compressedFileName)) {
            $zip->close();

            throw new \InvalidArgumentException(
                sprintf(
                    'File `%s` not found in ZIP archive `%s`.',
                    $compressedFileName,
                    $filePath
                )
            );
        }

        $zip->extractTo($tmpDir, $compressedFileName);
        $zip->close();

        rename($tmpDir . '/' . $compressedFileName, $tmpFile);

        return $tmpFile;
    }
}
