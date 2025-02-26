<?php


namespace Xibo\Event;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Xibo\Entity\Widget;

class XmdsConnectorFileEvent extends Event
{
    public static $NAME = 'connector.xmds.file.event';
    private $widget;
    private $response;
    /**
     * @var boolean
     */
    private $isDebug;

    public function __construct($widget, $isDebug = false)
    {
        $this->widget = $widget;
        $this->isDebug = $isDebug;
    }

    /**
     * @return \Xibo\Entity\Widget|null
     */
    public function getWidget(): ?Widget
    {
        return $this->widget;
    }

    public function isDebug(): bool
    {
        return $this->isDebug;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        if ($this->response === null) {
            $psr17Factory = new Psr17Factory();
            $this->response = $psr17Factory->createResponse(404, 'Not Found');
        }
        return $this->response;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return $this
     */
    public function setResponse(ResponseInterface $response): XmdsConnectorFileEvent
    {
        $this->response = $response;
        return $this;
    }
}
