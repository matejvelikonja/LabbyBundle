<?php

namespace Velikonja\LabbyBundle\Remote;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use SyncFS\Syncer as SyncerFs;

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
     * @param array          $config
     * @param ProcessBuilder $processBuilder
     * @param string|null    $executable
     */
    public function __construct(array $config, ProcessBuilder $processBuilder = null, $executable = null)
    {
        $this->config = $config;

        if (! $executable) {
            $executable = '/usr/bin/ssh';
        }

        if (! $processBuilder) {
            $processBuilder = new ProcessBuilder();
        }

        $processBuilder
            ->setPrefix($executable)
            ->setArguments(array(
                $config['hostname']
            ));

        $this->processBuilder = $processBuilder;
    }

    /**
     * @param string          $command
     * @param OutputInterface $output
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
     * @param string          $name
     * @param array           $arguments
     * @param OutputInterface $output
     *
     * @throws \Exception
     */
    public function execSf($name, array $arguments = array(), OutputInterface $output = null)
    {
        $command = $this->config['path'] . '/app/console ' . $name;

        $this->processBuilder->add($command);

        foreach ($arguments as $arg) {
            $this->processBuilder->add($arg);
        }

        $process = $this->processBuilder->getProcess();

        $process->run(
            $this->getCallback($output)
        );

        if (! $process->isSuccessful()) {
            throw new \Exception($process->getErrorOutput());
        }
    }

    /**
     * @param OutputInterface|null $output
     *
     * @return callable|null
     */
    private function getCallback(OutputInterface $output = null)
    {
        $callback = null;

        if ($output) {
            $callback = function ($type, $buffer) use ($output) {
                if (Process::ERR === $type) {
                    $output->writeln("<error>$buffer</error>");
                } else {
                    $output->writeln("$buffer");
                }
            };
        }

        return $callback;
    }
}