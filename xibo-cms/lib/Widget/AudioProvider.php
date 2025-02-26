<?php


namespace Xibo\Widget;

use Carbon\Carbon;
use Xibo\Support\Exception\NotFoundException;
use Xibo\Widget\Provider\DataProviderInterface;
use Xibo\Widget\Provider\DurationProviderInterface;
use Xibo\Widget\Provider\WidgetProviderInterface;
use Xibo\Widget\Provider\WidgetProviderTrait;

/**
 * Handles setting the correct audio duration.
 */
class AudioProvider implements WidgetProviderInterface
{
    use WidgetProviderTrait;

    public function fetchData(DataProviderInterface $dataProvider): WidgetProviderInterface
    {
        return $this;
    }

    public function fetchDuration(DurationProviderInterface $durationProvider): WidgetProviderInterface
    {
        // If we have not been provided a specific duration, we should use the duration stored in the library
        try {
            if ($durationProvider->getWidget()->useDuration === 0) {
                $durationProvider->setDuration($durationProvider->getWidget()->getDurationForMedia());
            }
        } catch (NotFoundException) {
            $this->getLog()->error('fetchDuration: video/audio without primaryMediaId. widgetId: '
                . $durationProvider->getWidget()->getId());
        }
        return $this;
    }

    public function getDataCacheKey(DataProviderInterface $dataProvider): ?string
    {
        // No special cache key requirements.
        return null;
    }

    public function getDataModifiedDt(DataProviderInterface $dataProvider): ?Carbon
    {
        return null;
    }
}
