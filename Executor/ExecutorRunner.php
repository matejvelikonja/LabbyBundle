<?php

namespace Velikonja\LabbyBundle\Executor;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Velikonja\LabbyBundle\Event\SyncEvent;
use Velikonja\LabbyBundle\Events;

class ExecutorRunner implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array|ExecutorInterface[]
     */
    private $executors;

    public function __construct(array $config)
    {
        $this->config    = $config;
        $this->executors = array();
    }

    /**
     * @param string $name
     * @param array  $args
     *
     * @throws \RuntimeException
     */
    public function __call($name, array $args)
    {
        if (! in_array($name, self::getSubscribedEvents())) {
            throw new \RuntimeException(
                sprintf(
                    'Method `%s` does not exists in `%s`.',
                    $name,
                    get_class()
                )
            );
        }

        /** @var SyncEvent $event */
        $event = $args[0];

        $this->runExecutors($name, $event->getOutput());
    }

    /**
     * @param string          $eventName
     * @param OutputInterface $output
     *
     * @throws \Exception
     */
    protected function runExecutors($eventName, OutputInterface $output)
    {
        if (! isset($this->config[$eventName])) {
            return;
        }

        foreach ($this->config[$eventName] as $commandConfig) {
            $executorName = key($commandConfig);
            $command      = reset($commandConfig);

            if (! isset($this->executors[$executorName])) {
                $availableExecutorNames = array_keys($this->executors);
                throw new \Exception(
                    sprintf(
                        'Executor `%s` not available. Registered executors: `%s`.',
                        $executorName,
                        implode(', ', $availableExecutorNames)
                    )
                );
            }

            $executor = $this->executors[$executorName];

            $output->writeln(
                sprintf(
                    'Executing command `%s` with executor `%s`.',
                    $command,
                    $executorName
                )
            );
            $executor->execute($command, $output);
        }
    }

    /**
     * @param ExecutorInterface $executor
     *
     * @return $this
     */
    public function addExecutor(ExecutorInterface $executor)
    {
        $this->executors[$executor->getName()] = $executor;

        return $this;
    }

    /**
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        $events = Events::all();

        return array_combine(array_values($events), array_values($events));
    }
}