<?php


namespace Xibo\Report;

use Xibo\Support\Exception\InvalidArgumentException;
use Xibo\Support\Sanitizer\SanitizerInterface;

/**
 * Common function between the Summary and Distribution reports
 */
trait SummaryDistributionCommonTrait
{
    /** @inheritdoc */
    public function restructureSavedReportOldJson($result)
    {
        $durationData = $result['durationData'];
        $countData = $result['countData'];
        $labels = $result['labels'];
        $backgroundColor = $result['backgroundColor'];
        $borderColor = $result['borderColor'];
        $periodStart = $result['periodStart'];
        $periodEnd = $result['periodEnd'];

        return [
            'hasData' => count($durationData) > 0 && count($countData) > 0,
            'chart' => [
                'type' => 'bar',
                'data' => [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => __('Total duration'),
                            'yAxisID' => 'Duration',
                            'backgroundColor' => $backgroundColor,
                            'data' => $durationData
                        ],
                        [
                            'label' => __('Total count'),
                            'yAxisID' => 'Count',
                            'borderColor' => $borderColor,
                            'type' => 'line',
                            'fill' => false,
                            'data' =>  $countData
                        ]
                    ]
                ],
                'options' => [
                    'scales' => [
                        'yAxes' => [
                            [
                                'id' => 'Duration',
                                'type' => 'linear',
                                'position' =>  'left',
                                'display' =>  true,
                                'scaleLabel' =>  [
                                    'display' =>  true,
                                    'labelString' => __('Duration(s)')
                                ],
                                'ticks' =>  [
                                    'beginAtZero' => true
                                ]
                            ], [
                                'id' => 'Count',
                                'type' => 'linear',
                                'position' =>  'right',
                                'display' =>  true,
                                'scaleLabel' =>  [
                                    'display' =>  true,
                                    'labelString' => __('Count')
                                ],
                                'ticks' =>  [
                                    'beginAtZero' => true
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,

        ];
    }

    /**
     * @param \Xibo\Support\Sanitizer\SanitizerInterface $sanitizedParams
     * @return array
     * @throws \Xibo\Support\Exception\InvalidArgumentException
     * @throws \Xibo\Support\Exception\NotFoundException
     */
    private function getReportScheduleFormTitle(SanitizerInterface $sanitizedParams): array
    {
        $type = $sanitizedParams->getString('type');
        if ($type == 'layout') {
            $selectedId = $sanitizedParams->getInt('layoutId');
            $title = sprintf(
                __('Add Report Schedule for %s - %s'),
                $type,
                $this->layoutFactory->getById($selectedId)->layout
            );
        } elseif ($type == 'media') {
            $selectedId = $sanitizedParams->getInt('mediaId');
            $title = sprintf(
                __('Add Report Schedule for %s - %s'),
                $type,
                $this->mediaFactory->getById($selectedId)->name
            );
        } elseif ($type == 'event') {
            $selectedId = 0; // we only need eventTag
            $eventTag = $sanitizedParams->getString('eventTag');
            $title = sprintf(
                __('Add Report Schedule for %s - %s'),
                $type,
                $eventTag
            );
        } else {
            throw new InvalidArgumentException(__('Unknown type ') . $type, 'type');
        }
        
        return [
            'title' => $title,
            'selectedId' => $selectedId
        ];
    }
}
