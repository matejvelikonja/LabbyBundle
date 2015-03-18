<?php

namespace Velikonja\LabbyBundle\Database;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Velikonja\LabbyBundle\Executor\SymfonyCommandExecutor;

class CreatorDb
{
    /**
     * @var SymfonyCommandExecutor
     */
    private $executor;

    /**
     * @param SymfonyCommandExecutor $executor
     */
    public function __construct(SymfonyCommandExecutor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @param OutputInterface $output
     */
    public function create(OutputInterface $output = null)
    {
        $bufferedOutput = new BufferedOutput(OutputInterface::VERBOSITY_NORMAL, true);
        $error          = false;

        try {
            $this->executor->execute('doctrine:database:drop --force', $bufferedOutput);
        } catch (\RuntimeException $e) {
            // catches an error if database does not exist
            $error = true;
        }

        if (! $error) {
            $output->write($bufferedOutput->fetch());
        }

        $this->executor->execute('doctrine:database:create', $output);
    }
}
