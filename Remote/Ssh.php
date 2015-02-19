<?php

namespace Velikonja\LabbyBundle\Remote;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

class Ssh
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var ProcessBuilder
     */
    private $processBuilder;

    /**
     * @param array               $config
     * @param int                 $timeout
     * @param string|null         $executable
     * @param null|ProcessBuilder $processBuilder
     */
    public function __construct(array $config, $timeout = 60, $executable = null, ProcessBuilder $processBuilder = null)
    {
        $this->config = $config;

        if (! $executable) {
            $executable = '/usr/bin/ssh';
        }

        if (! $processBuilder) {
            $processBuilder = new ProcessBuilder();
        }

        $processBuilder
            ->setTimeout($timeout)
            ->setPrefix($executable)
            ->setArguments(array(
                $config['hostname']
            ));

        $this->processBuilder = $processBuilder;
    }

    /**
     * @param string               $command
     * @param null|OutputInterface $output
     *
     * @throws \Exception
     */
    public function exec($command, OutputInterface $output = null)
    {
        $process = $this->processBuilder->add($command)->getProcess();
        $process->run(
            $this->getCallback($output)
        );

        if (! $process->isSuccessful()) {
            throw new \Exception($process->getErrorOutput());
        }
    }

    /**
     * Executes Symfony2 command on remote.
     *
     * @param string        $executable
     * @param string[]      $arguments
     * @param callable|null $callback
     *
     * @throws \Exception
     */
    public function execSf($executable, array $arguments = array(), $callback = null)
    {
        $executable = $this->config['path'] . '/app/console ' . $executable;

        $this->processBuilder->add($executable);

        // append sf environment to be run on remote
        $arguments[] = '--env=' . $this->config['env'];

        foreach ($arguments as $arg) {
            $this->processBuilder->add($arg);
        }

        $process = $this->processBuilder->getProcess();

        $process->run($callback);

        if (! $process->isSuccessful()) {
            throw new \Exception($process->getErrorOutput());
        }
    }
}
