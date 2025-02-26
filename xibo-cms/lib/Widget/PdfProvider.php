<?php


namespace Xibo\Widget;

use Carbon\Carbon;
use Mpdf\Mpdf;
use Xibo\Widget\Provider\DataProviderInterface;
use Xibo\Widget\Provider\DurationProviderInterface;
use Xibo\Widget\Provider\WidgetProviderInterface;
use Xibo\Widget\Provider\WidgetProviderTrait;

/**
 * PDF provider to calculate the duration if durationIsPerItem is selected.
 */
class PdfProvider implements WidgetProviderInterface
{
    use WidgetProviderTrait;

    public function fetchData(DataProviderInterface $dataProvider): WidgetProviderInterface
    {
        return $this;
    }

    public function fetchDuration(DurationProviderInterface $durationProvider): WidgetProviderInterface
    {
        $widget = $durationProvider->getWidget();
        if ($widget->getOptionValue('durationIsPerItem', 0) == 1) {
            // Do we already have an option stored for the number of pages?
            $pageCount = 1;
            $cachedPageCount = $widget->getOptionValue('pageCount', null);
            if ($cachedPageCount === null) {
                try {
                    $sourceFile = $widget->getPrimaryMediaPath();

                    $this->getLog()->debug('fetchDuration: loading PDF file to get the number of pages, file: '
                        . $sourceFile);

                    $mPdf = new Mpdf([
                        'tempDir' => $widget->getLibraryTempPath(),
                    ]);
                    $pageCount = $mPdf->setSourceFile($sourceFile);

                    $widget->setOptionValue('pageCount', 'attrib', $pageCount);
                } catch (\Exception $e) {
                    $this->getLog()->error('fetchDuration: unable to get PDF page count, e: ' . $e->getMessage());
                }
            } else {
                $pageCount = $cachedPageCount;
            }

            $durationProvider->setDuration($durationProvider->getWidget()->calculatedDuration * $pageCount);
        }
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
