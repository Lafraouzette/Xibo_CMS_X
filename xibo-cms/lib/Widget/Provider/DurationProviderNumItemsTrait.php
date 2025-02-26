<?php


namespace Xibo\Widget\Provider;

/**
 * A trait providing the duration for widgets using numItems, durationIsPerItem and itemsPerPage
 */
trait DurationProviderNumItemsTrait
{
    public function fetchDuration(DurationProviderInterface $durationProvider): WidgetProviderInterface
    {
        $this->getLog()->debug('fetchDuration: DurationProviderNumItemsTrait');

        // Take some default action to cover the majourity of region specific widgets
        // Duration can depend on the number of items per page for some widgets
        // this is a legacy way of working, and our preference is to use elements
        $numItems = $durationProvider->getWidget()->getOptionValue('numItems', 15);

        if ($durationProvider->getWidget()->getOptionValue('durationIsPerItem', 0) == 1 && $numItems > 1) {
            // If we have paging involved then work out the page count.
            $itemsPerPage = $durationProvider->getWidget()->getOptionValue('itemsPerPage', 0);
            if ($itemsPerPage > 0) {
                $numItems = ceil($numItems / $itemsPerPage);
            }

            $durationProvider->setDuration($durationProvider->getWidget()->calculatedDuration * $numItems);
        }
        return $this;
    }
}
