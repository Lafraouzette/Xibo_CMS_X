<?php


namespace Xibo\Listener;

use Stash\Interfaces\PoolInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Xibo\Event\TriggerTaskEvent;
use Xibo\Factory\TaskFactory;
use Xibo\Service\ConfigServiceInterface;

/**
 * A listener for events related to tasks
 */
class TaskListener
{
    use ListenerLoggerTrait;

    public function __construct(
        private readonly TaskFactory $taskFactory,
        private readonly ConfigServiceInterface $configService,
        private readonly PoolInterface $pool
    ) {
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     * @return $this
     */
    public function registerWithDispatcher(EventDispatcherInterface $dispatcher) : TaskListener
    {
        $dispatcher->addListener(TriggerTaskEvent::$NAME, [$this, 'onTriggerTask']);

        return $this;
    }

    /**
     * @param TriggerTaskEvent $event
     * @return void
     * @throws \Xibo\Support\Exception\InvalidArgumentException
     * @throws \Xibo\Support\Exception\NotFoundException
     */
    public function onTriggerTask(TriggerTaskEvent $event): void
    {
        if (!empty($event->getKey())) {
            // Drop this setting from the cache
            $this->pool->deleteItem($event->getKey());
        }

        // Mark the task to run now
        $task = $this->taskFactory->getByClass($event->getClassName());
        $task->runNow = 1;
        $task->save();
    }
}
