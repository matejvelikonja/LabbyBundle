<?php

namespace Velikonja\LabbyBundle\Executor;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

class SymfonyCommandExecutor implements ExecutorInterface
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @param string               $command
     * @param OutputInterface|null $output
     *
     * @return int
     */
    public function execute($command, OutputInterface $output = null)
    {
        $this->app->setAutoExit(false);

        $input = new StringInput($command);

        $exitCode = $this->app->run($input, $output);

        if ($exitCode != 0) {
            throw new \RuntimeException(
                sprintf('Command `%s` failed.', $command)
            );
        }

        $this->app->setAutoExit(true);

        return $exitCode;
    }

    /**
     * @param ConsoleCommandEvent $event
     */
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $this->app = $event->getCommand()->getApplication();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sf';
    }
}
