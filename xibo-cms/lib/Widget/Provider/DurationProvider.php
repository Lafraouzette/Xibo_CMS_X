<?php


namespace Xibo\Widget\Provider;

use Xibo\Entity\Module;
use Xibo\Entity\Widget;

/**
 * Xibo's default implementation of the Duration Provider
 */
class DurationProvider implements DurationProviderInterface
{
    /** @var Module */
    private $module;

    /** @var Widget */
    private $widget;

    /** @var int Duration in seconds */
    private $duration;

    /** @var bool Has the duration been set? */
    private $isDurationSet = false;

    /**
     * Constructor
     * @param Module $module
     * @param Widget $widget
     */
    public function __construct(Module $module, Widget $widget)
    {
        $this->module = $module;
        $this->widget = $widget;
    }

    /**
     * @inheritDoc
     */
    public function setDuration(int $seconds): DurationProviderInterface
    {
        $this->isDurationSet = true;
        $this->duration = $seconds;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDuration(): int
    {
        return $this->duration ?? 0;
    }

    /**
     * @inheritDoc
     */
    public function isDurationSet(): bool
    {
        return $this->isDurationSet;
    }

    /**
     * @inheritDoc
     */
    public function getModule(): Module
    {
        return $this->module;
    }

    /**
     * @inheritDoc
     */
    public function getWidget(): Widget
    {
        return $this->widget;
    }
}
