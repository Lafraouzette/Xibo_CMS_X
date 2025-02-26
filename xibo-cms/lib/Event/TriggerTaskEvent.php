<?php


namespace Xibo\Event;

/**
 * An event which triggers the provided task to Run Now (at the next XTR poll)
 *  optionally clears a cache key to provide further instructions to the task that's running
 */
class TriggerTaskEvent extends Event
{
    public static string $NAME = 'trigger.task.event';

    /**
     * @param string $className Class name of the task to be run
     * @param string $key Cache Key to be dropped
     */
    public function __construct(
        private readonly string $className,
        private readonly string $key = ''
    ) {
    }

    /**
     * Returns the class name for the task to be run
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * Returns the cache key to be dropped
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
