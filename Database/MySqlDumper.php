<?php

namespace Velikonja\LabbyBundle\Database;

use Symfony\Component\Process\ProcessBuilder;

class MySqlDumper
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
            $executable = '/usr/bin/mysqldump';
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
                $options['dbname'],
            ));

        $this->processBuilder = $processBuilder;

    }

    /**
     * @throws DatabaseException
     *
     * @return string
     */
    public function dump()
    {
        $process = $this->processBuilder->getProcess();

        $process->run();

        if (! $process->isSuccessful()) {
            throw new DatabaseException($process->getErrorOutput());
        }

        return $process->getOutput();
    }
}