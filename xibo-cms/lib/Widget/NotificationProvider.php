<?php


namespace Xibo\Widget;

use Carbon\Carbon;
use Xibo\Event\NotificationDataRequestEvent;
use Xibo\Event\NotificationModifiedDtRequestEvent;
use Xibo\Widget\Provider\DataProviderInterface;
use Xibo\Widget\Provider\DurationProviderNumItemsTrait;
use Xibo\Widget\Provider\WidgetProviderInterface;
use Xibo\Widget\Provider\WidgetProviderTrait;

class NotificationProvider implements WidgetProviderInterface
{
    use WidgetProviderTrait;
    use DurationProviderNumItemsTrait;

    public function fetchData(DataProviderInterface $dataProvider): WidgetProviderInterface
    {
        $this->getLog()->debug('fetchData: NotificationProvider passing to event');
        $this->getDispatcher()->dispatch(
            new NotificationDataRequestEvent($dataProvider),
            NotificationDataRequestEvent::$NAME
        );
        return $this;
    }

    public function getDataCacheKey(DataProviderInterface $dataProvider): ?string
    {
        return null;
    }

    public function getDataModifiedDt(DataProviderInterface $dataProvider): ?Carbon
    {
        $event = new NotificationModifiedDtRequestEvent($dataProvider->getDisplayId());
        $this->getDispatcher()->dispatch($event, NotificationModifiedDtRequestEvent::$NAME);
        return $event->getModifiedDt();
    }
}
