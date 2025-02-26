<?php


namespace Xibo\Widget\Provider;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * A trait to set common objects on a Widget Compatibility Interface
 */
trait WidgetValidatorTrait
{
    private $log;

    public function getLog(): LoggerInterface
    {
        if ($this->log === null) {
            $this->log = new NullLogger();
        }
        return $this->log;
    }

    public function setLog(LoggerInterface $logger): WidgetValidatorInterface
    {
        $this->log = $logger;
        return $this;
    }
}
