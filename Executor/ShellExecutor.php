<?php

namespace Velikonja\LabbyBundle\Executor;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ShellExecutor implements ExecutorInterface
{
    /**
     * @param string               $command
     * @param OutputInterface|null $output
     *
     * @return int
     */
    public function execute($command, OutputInterface $output = null)
    {
        $process = new Process($command);
        $process->setTimeout(120);

        $process->run(function ($type, $buffer) use ($output) {
            $output->writeln($buffer);
        });

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process->getExitCode();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'shell';
    }
}
