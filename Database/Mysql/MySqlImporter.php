<?php

namespace Velikonja\LabbyBundle\Database\Mysql;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Velikonja\LabbyBundle\Database\DatabaseException;
use Velikonja\LabbyBundle\Database\ImporterInterface;

class MySqlImporter implements ImporterInterface
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
     * @param array               $options
     * @param int                 $timeout
     * @param null|string         $executable
     * @param null|ProcessBuilder $processBuilder
     */
    public function __construct(
        array $options,
        $timeout = 60,
        $executable = null,
        ProcessBuilder $processBuilder = null
    ) {
        if (! $executable) {
            $executable = '/usr/bin/mysql';
        }

        $this->executable = $executable;

        if (! $processBuilder) {
            $processBuilder = new ProcessBuilder();
        }

        $processBuilder
            ->setTimeout($timeout)
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
     * Import dump to database.
     *
     * @param string        $file
     * @param null|callable $callback
     *
     * @throws DatabaseException
     *
     * @return void
     */
    public function import($file, $callback = null)
    {
        $process = $this->processBuilder->getProcess();

        $dump = file_get_contents($file);

        if (method_exists($process, 'setInput')) {
            $process->setInput($dump);
        } else {
            // support for SF2.3
            $process->setStdin($dump);
        }

        if ($callback) {
            call_user_func($callback, Process::OUT, 'Running shell command: ' . $process->getCommandLine());
        }

        $process->run($callback);

        if (! $process->isSuccessful()) {
            throw new DatabaseException($process->getErrorOutput());
        }
    }
}
