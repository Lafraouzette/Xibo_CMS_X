<?php


namespace Xibo\Widget;

use Carbon\Carbon;
use Xibo\Event\DashboardDataRequestEvent;
use Xibo\Widget\Provider\DataProviderInterface;
use Xibo\Widget\Provider\DurationProviderInterface;
use Xibo\Widget\Provider\WidgetProviderInterface;
use Xibo\Widget\Provider\WidgetProviderTrait;

class DashboardProvider implements WidgetProviderInterface
{
    use WidgetProviderTrait;

    public function fetchData(DataProviderInterface $dataProvider): WidgetProviderInterface
    {
        $this->getLog()->debug('fetchData: DashboardProvider passing to event');
        $this->getDispatcher()->dispatch(
            new DashboardDataRequestEvent($dataProvider),
            DashboardDataRequestEvent::$NAME
        );
        return $this;
    }

    public function fetchDuration(DurationProviderInterface $durationProvider): WidgetProviderInterface
    {
        return $this;
    }

    public function getDataCacheKey(DataProviderInterface $dataProvider): ?string
    {
        return null;
    }

    public function getDataModifiedDt(DataProviderInterface $dataProvider): ?Carbon
    {
        return null;
    }
}
