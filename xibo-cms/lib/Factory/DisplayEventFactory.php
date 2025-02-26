<?php



namespace Xibo\Factory;
use Xibo\Entity\DisplayEvent;

/**
 * Class DisplayEventFactory
 * @package Xibo\Factory
 */
class DisplayEventFactory extends BaseFactory
{
    /**
     * @return DisplayEvent
     */
    public function createEmpty()
    {
        return new DisplayEvent($this->getStore(), $this->getLog(), $this->getDispatcher());
    }
}