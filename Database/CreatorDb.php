<?php

namespace Velikonja\LabbyBundle\Database;

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
        $this->executor->execute('doctrine:database:drop --force', $output);
        $this->executor->execute('doctrine:database:create', $output);
    }
}
