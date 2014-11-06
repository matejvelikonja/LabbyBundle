<?php

namespace Velikonja\LabbyBundle\Database;

use Symfony\Component\Process\ProcessBuilder;

class MySqlImporter
{
    /**
     * @var \Symfony\Component\Process\ProcessBuilder
     */
    private $processBuilder;

    /**
     * @var string
     */
    private $executable;

    /**
     * @param array          $options
     * @param ProcessBuilder $processBuilder
     * @param string         $executable
     */
    public function __construct(array $options, ProcessBuilder $processBuilder = null, $executable = null)
    {
        if (! $executable) {
            $executable = '/usr/bin/mysql';
        }

        $this->executable = $executable;

        if (! $processBuilder) {
            $processBuilder = new ProcessBuilder();
        }

        $processBuilder
            ->setPrefix($this->executable)
            ->setArguments(array(
                '--user=' . $options['user'],
                '--password=' . $options['password'],
                '--host=' . $options['host'],
                '--database=' . $options['dbname'],
            ));

        $this->processBuilder = $processBuilder;

    }

    /**
     * @param string $file
     *
     * @throws DatabaseException
     */
    public function import($file)
    {
        $process = $this->processBuilder->getProcess();

        $dump = file_get_contents($file);

        $process->setInput($dump);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new DatabaseException($process->getErrorOutput());
        }
    }
}
