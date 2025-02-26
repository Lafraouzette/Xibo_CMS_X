<?php

namespace Xibo\Event;

use Xibo\Widget\Provider\DataProviderInterface;

/**
 * Event raised when a widget requests data.
 */
class WidgetDataRequestEvent extends Event
{
    public static $NAME = 'widget.data.request.event';
    
    /** @var \Xibo\Widget\Provider\DataProviderInterface */
    private $dataProvider;

    public function __construct(DataProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * The data provider should be updated with data for its widget.
     * @return \Xibo\Widget\Provider\DataProviderInterface
     */
    public function getDataProvider(): DataProviderInterface
    {
        return $this->dataProvider;
    }
}
