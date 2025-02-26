<?php


namespace Xibo\Widget\Provider;

use Xibo\Entity\Module;
use Xibo\Entity\Widget;

/**
 * A duration provider is used to return the duration for a Widget which has a media file
 */
interface DurationProviderInterface
{
    /**
     * Get the Module
     * @return Module
     */
    public function getModule(): Module;

    /**
     * Get the Widget
     * @return Widget
     */
    public function getWidget(): Widget;

    /**
     * Get the duration
     * @return int the duration in seconds
     */
    public function getDuration(): int;

    /**
     * Set the duration in seconds
     * @param int $seconds the duration in seconds
     * @return \Xibo\Widget\Provider\DurationProviderInterface
     */
    public function setDuration(int $seconds): DurationProviderInterface;

    /**
     * @return bool true if the duration has been set
     */
    public function isDurationSet(): bool;
}
