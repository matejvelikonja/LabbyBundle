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
        $error     = false;
        $outputTmp = null;

        // SF 2.3 does not have BufferedOutput
        if (class_exists('Symfony\Component\Console\Output\BufferedOutput')) {
            $outputTmp = new BufferedOutput(OutputInterface::VERBOSITY_NORMAL, true);
        }

        try {
            $this->executor->execute('doctrine:database:drop --force', $outputTmp);
        } catch (\RuntimeException $e) {
            // catches an error if database does not exist
            $error = true;
        }

        if (! $error && $outputTmp) {
            $output->write($outputTmp->fetch());
        }

        $this->executor->execute('doctrine:database:create', $output);
    }
}
