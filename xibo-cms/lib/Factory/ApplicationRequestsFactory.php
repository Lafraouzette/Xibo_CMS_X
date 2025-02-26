<?php


namespace Xibo\Factory;

use Xibo\Entity\ApplicationRequest;

/**
 * Class ApplicationRequestsFactory
 * @package Xibo\Factory
 */
class ApplicationRequestsFactory extends BaseFactory
{
    /**
     * @return ApplicationRequest
     */
    public function createEmpty(): ApplicationRequest
    {
        return new ApplicationRequest($this->getStore(), $this->getLog(), $this->getDispatcher());
    }
}
