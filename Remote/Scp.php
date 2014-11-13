<?php

namespace Velikonja\LabbyBundle\Remote;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class Scp
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
     * @param null|ProcessBuilder $processBuilder
     * @param string|null         $executable
     */
    public function __construct(array $config, ProcessBuilder $processBuilder = null, $executable = null)
    {
        $this->config = $config;

        if (! $executable) {
            $executable = '/usr/bin/scp';
        }

        if (! $processBuilder) {
            $processBuilder = new ProcessBuilder();
        }

        $processBuilder
            ->setPrefix($executable);

        $this->processBuilder = $processBuilder;
    }

    /**
     * @param string               $src
     * @param string               $dst
     * @param null|OutputInterface $output
     *
     * @throws \Exception
     */
    public function copyFile($src, $dst, OutputInterface $output = null)
    {
        $process = $this->processBuilder
            ->add($this->config['hostname'] . ':' . $src)
            ->add($dst)
            ->getProcess();

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
     * @return \Closure|null
     */
    private function getCallback(OutputInterface $output = null)
    {
        $callback = null;

        if ($output) {
            $callback = function ($type, $buffer) use ($output) {
                if (Process::ERR === $type) {
                    $output->writeln("<error>$buffer</error>");
                } else {
                    $output->writeln($buffer);
                }
            };
        }

        return $callback;
    }
}
