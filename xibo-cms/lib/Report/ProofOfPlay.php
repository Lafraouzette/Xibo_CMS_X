<?php

namespace Xibo\Report;

use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use Psr\Container\ContainerInterface;
use Xibo\Controller\DataTablesDotNetTrait;
use Xibo\Entity\ReportForm;
use Xibo\Entity\ReportResult;
use Xibo\Entity\ReportSchedule;
use Xibo\Factory\DisplayFactory;
use Xibo\Factory\LayoutFactory;
use Xibo\Factory\MediaFactory;
use Xibo\Factory\ReportScheduleFactory;
use Xibo\Factory\DisplayGroupFactory;
use Xibo\Factory\TagFactory;
use Xibo\Helper\ApplicationState;
use Xibo\Helper\DateFormatHelper;
use Xibo\Helper\SanitizerService;
use Xibo\Helper\Translate;
use Xibo\Support\Exception\GeneralException;
use Xibo\Support\Exception\InvalidArgumentException;
use Xibo\Support\Exception\NotFoundException;
use Xibo\Support\Sanitizer\SanitizerInterface;

/**
 * Class ProofOfPlay
 * @package Xibo\Report
 */
class ProofOfPlay implements ReportInterface
{
    use ReportDefaultTrait, DataTablesDotNetTrait;

    /**
     * @var DisplayFactory
     */
    private $displayFactory;

    /**
     * @var MediaFactory
     */
    private $mediaFactory;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var ReportScheduleFactory
     */
    private $reportScheduleFactory;

    /**
     * @var DisplayGroupFactory
     */
    private $displayGroupFactory;

    /**
     * @var TagFactory
     */
    private $tagFactory;

    /**
     * @var SanitizerService
     */
    private $sanitizer;

    /**
     * @var ApplicationState
     */
    private $state;

    private $table = 'stat';

    private $tagsType = [
        'dg' => 'Display group',
        'media' => 'Media',
        'layout' => 'Layout'
    ];

    /** @inheritdoc */
    public function setFactories(ContainerInterface $container)
    {
        $this->displayFactory = $container->get('displayFactory');
        $this->mediaFactory = $container->get('mediaFactory');
        $this->layoutFactory = $container->get('layoutFactory');
        $this->reportScheduleFactory = $container->get('reportScheduleFactory');
        $this->displayGroupFactory = $container->get('displayGroupFactory');
        $this->tagFactory = $container->get('tagFactory');
        $this->sanitizer = $container->get('sanitizerService');

        return $this;
    }

    /** @inheritdoc */
    public function getReportEmailTemplate()
    {
        return 'proofofplay-email-template.twig';
    }

    /** @inheritdoc */
    public function getSavedReportTemplate()
    {
        return 'proofofplay-report-preview';
    }

    /** @inheritdoc */
    public function getReportForm()
    {
        return new ReportForm(
            'proofofplay-report-form',
            'proofofplayReport',
            'Proof of Play',
            [
                'fromDateOneDay' => Carbon::now()->subSeconds(86400)->format(DateFormatHelper::getSystemFormat()),
                'toDate' => Carbon::now()->format(DateFormatHelper::getSystemFormat())
            ],
            __('Select a type and an item (i.e., layout/media/tag)')
        );
    }

    /** @inheritdoc */
    public function getReportScheduleFormData(SanitizerInterface $sanitizedParams)
    {
        $data = [];
        $data['type'] = $sanitizedParams->getString('type');
        $data['tagsType'] = $sanitizedParams->getString('tagsType');

        $exactTags = $sanitizedParams->getCheckbox('exactTags');
        $data['exactTags'] = $exactTags == 'true';

        $tags = $sanitizedParams->getString('tags');
        $data['tags'] = $tags;

        $data['hiddenFields'] =  '';
        $data['reportName'] = 'proofofplayReport';

        return [
            'template' => 'proofofplay-schedule-form-add',
            'data' => $data
        ];
    }

    /** @inheritdoc */
    public function setReportScheduleFormData(SanitizerInterface $sanitizedParams)
    {
        $filter = $sanitizedParams->getString('filter');
        $filterCriteria = [
            'filter' => $filter,
            'displayId' => $sanitizedParams->getInt('displayId'),
            'layoutId' => $sanitizedParams->getIntArray('layoutId'),
            'mediaId' => $sanitizedParams->getIntArray('mediaId'),
            'type' => $sanitizedParams->getString('type'),
            'sortBy' => $sanitizedParams->getString('sortBy'),
            'tagsType' => $sanitizedParams->getString('tagsType'),
            'tags' => $sanitizedParams->getString('tags'),
            'exactTags' => $sanitizedParams->getCheckbox('exactTags'),
            'logicalOperator' => $sanitizedParams->getString('logicalOperator')
        ];

        $schedule = '';
        if ($filter == 'daily') {
            $schedule = ReportSchedule::$SCHEDULE_DAILY;
            $filterCriteria['reportFilter'] = 'yesterday';
        } elseif ($filter == 'weekly') {
            $schedule = ReportSchedule::$SCHEDULE_WEEKLY;
            $filterCriteria['reportFilter'] = 'lastweek';
        } elseif ($filter == 'monthly') {
            $schedule = ReportSchedule::$SCHEDULE_MONTHLY;
            $filterCriteria['reportFilter'] = 'lastmonth';
        } elseif ($filter == 'yearly') {
            $schedule = ReportSchedule::$SCHEDULE_YEARLY;
            $filterCriteria['reportFilter'] = 'lastyear';
        }

        $filterCriteria['sendEmail'] = $sanitizedParams->getCheckbox('sendEmail');
        $filterCriteria['nonusers'] = $sanitizedParams->getString('nonusers');

        // Return
        return [
            'filterCriteria' => json_encode($filterCriteria),
            'schedule' => $schedule
        ];
    }

    /** @inheritdoc */
    public function generateSavedReportName(SanitizerInterface $sanitizedParams)
    {
        $saveAs = sprintf(__('%s report for ', ucfirst($sanitizedParams->getString('filter'))));

        switch ($sanitizedParams->getString('type')) {
            case 'layout':
                $saveAs .= 'Type: Layout. ';
                break;

            case 'media':
                $saveAs .= 'Type: Media. ';
                break;

            case 'widget':
                $saveAs .= 'Type: Widget. ';
                break;

            case 'event':
                $saveAs .= 'Type: Event. ';
                break;

            default:
                $saveAs .= 'Type: All. ';
                break;
        }

        $layoutIds = $sanitizedParams->getIntArray('layoutIds');
        if (isset($layoutIds)) {
            if (count($layoutIds) > 0) {
                $layouts = '';
                foreach ($layoutIds as $id) {
                    try {
                        $layout = $this->layoutFactory->getById($id);
                    } catch (NotFoundException $error) {
                        // Get the campaign ID
                        $campaignId = $this->layoutFactory->getCampaignIdFromLayoutHistory($id);
                        $layoutId = $this->layoutFactory->getLatestLayoutIdFromLayoutHistory($campaignId);
                        $layout = $this->layoutFactory->getById($layoutId);
                    }

                    $layouts .= $layout->layout . ', ';
                }

                $saveAs .= 'Layouts: '. $layouts;
            }
        }

        $mediaIds = $sanitizedParams->getIntArray('mediaIds');
        if (isset($mediaIds)) {
            if (count($mediaIds) > 0) {
                $medias = '';
                foreach ($mediaIds as $id) {
                    try {
                        $media = $this->mediaFactory->getById($id);
                        $name = $media->name;
                    } catch (NotFoundException $error) {
                        $name = 'Media not found';
                    }

                    $medias .= $name . ', ';
                }

                $saveAs .= 'Media: ' . $medias;
            }
        }

        $displayId = $sanitizedParams->getInt('displayId');
        if (!empty($displayId)) {
            // Get display
            try {
                $displayName = $this->displayFactory->getById($displayId)->display;
                $saveAs .= '(Display: '. $displayName . ')';
            } catch (NotFoundException $error) {
                $saveAs .= '(DisplayId: Not Found )';
            }
        }

        return $saveAs;
    }

    /** @inheritdoc */
    public function restructureSavedReportOldJson($result) // TODO
    {
        return [
            'periodStart' => $result['periodStart'],
            'periodEnd' => $result['periodEnd'],
            'table' => $result['result'],
        ];
    }

    /** @inheritdoc */
    public function getSavedReportResults($json, $savedReport)
    {
        // Get filter criteria
        $rs = $this->reportScheduleFactory->getById($savedReport->reportScheduleId, 1)->filterCriteria;
        $filterCriteria = json_decode($rs, true);

        $tagsType = $filterCriteria['tagsType'];
        $tags = $filterCriteria['tags'];
        $exactTags = ($filterCriteria['exactTags'] == 1) ? ' (exact match)': '';

        // Show filter criteria
        $metadata = [];
        if ($tags != null) {
            $metadata['filterInfo'] = 'Tags from: '. $this->tagsType[$tagsType]. ', Tags: '. $tags. $exactTags;
        }

        // Get Meta data
        $metadata['periodStart'] = $json['metadata']['periodStart'];
        $metadata['periodEnd'] = $json['metadata']['periodEnd'];
        $metadata['generatedOn'] = Carbon::createFromTimestamp($savedReport->generatedOn)
            ->format(DateFormatHelper::getSystemFormat());
        $metadata['title'] = $savedReport->saveAs;

        // Report result object
        return new ReportResult(
            $metadata,
            $json['table'],
            $json['recordsTotal']
        );
    }

    /** @inheritdoc */
    public function getResults(SanitizerInterface $sanitizedParams)
    {
        $layoutIds = $sanitizedParams->getIntArray('layoutId', ['default' => []]);
        $mediaIds = $sanitizedParams->getIntArray('mediaId', ['default' => []]);
        $type = strtolower($sanitizedParams->getString('type'));
        $tags = $sanitizedParams->getString('tags');
        $tagsType = $sanitizedParams->getString('tagsType');
        $exactTags = $sanitizedParams->getCheckbox('exactTags');
        $operator = $sanitizedParams->getString('logicalOperator', ['default' => 'OR']);
        $parentCampaignId = $sanitizedParams->getInt('parentCampaignId');

        // Group the data by display, display group, or by tag
        $groupBy = $sanitizedParams->getString('groupBy');

        // Used with groupBy in case we want to filter by specific display groups only
        $displayGroupIds = $sanitizedParams->getIntArray('displayGroupId', ['default' => []]);

        // Display filter.
        try {
            // Get an array of display id this user has access to.
            $displayIds = $this->getDisplayIdFilter($sanitizedParams);
        } catch (GeneralException $exception) {
            // stop the query
            return new ReportResult();
        }

        // web
        if ($sanitizedParams->getString('sortBy') == null) {
            // Sorting?
            $sortOrder = $this->gridRenderSort($sanitizedParams);
            $columns = [];

            if (is_array($sortOrder)) {
                $columns = $sortOrder;
            }
        } else {
            $sortBy = $sanitizedParams->getString('sortBy', ['default' => 'widgetId']);
            if (!in_array($sortBy, [
                'widgetId',
                'type',
                'display',
                'displayId',
                'media',
                'layout',
                'layoutId',
                'tag',
            ])) {
                throw new InvalidArgumentException(__('Invalid Sort By'), 'sortBy');
            }
            $columns = [$sortBy];
        }

        //
        // From and To Date Selection
        // --------------------------
        // Our report has a range filter which determines whether the user has to enter their own from / to dates
        // check the range filter first and set from/to dates accordingly.
        $reportFilter = $sanitizedParams->getString('reportFilter');

        // Use the current date as a helper
        $now = Carbon::now();

        switch ($reportFilter) {
            case 'today':
                $fromDt = $now->copy()->startOfDay();
                $toDt = $fromDt->copy()->addDay();
                break;

            case 'yesterday':
                $fromDt = $now->copy()->startOfDay()->subDay();
                $toDt = $now->copy()->startOfDay();
                break;

            case 'thisweek':
                $fromDt = $now->copy()->locale(Translate::GetLocale())->startOfWeek();
                $toDt = $fromDt->copy()->addWeek();
                break;

            case 'thismonth':
                $fromDt = $now->copy()->startOfMonth();
                $toDt = $fromDt->copy()->addMonth();
                break;

            case 'thisyear':
                $fromDt = $now->copy()->startOfYear();
                $toDt = $fromDt->copy()->addYear();
                break;

            case 'lastweek':
                $fromDt = $now->copy()->locale(Translate::GetLocale())->startOfWeek()->subWeek();
                $toDt = $fromDt->copy()->addWeek();
                break;

            case 'lastmonth':
                $fromDt = $now->copy()->startOfMonth()->subMonth();
                $toDt = $fromDt->copy()->addMonth();
                break;

            case 'lastyear':
                $fromDt = $now->copy()->startOfYear()->subYear();
                $toDt = $fromDt->copy()->addYear();
                break;

            case '':
            default:
                // Expect dates to be provided.
                $fromDt = $sanitizedParams->getDate('statsFromDt', ['default' => Carbon::now()->subDay()]);
                $fromDt->startOfDay();

                $toDt = $sanitizedParams->getDate('statsToDt', ['default' => Carbon::now()]);
                $toDt->startOfDay();

                $fromDtTime = $sanitizedParams->getString('statsFromDtTime');
                $toDtTime = $sanitizedParams->getString('statsToDtTime');

                if ($fromDtTime !== null && $toDtTime !== null) {
                    $startTimeArray = explode(':', $fromDtTime);
                    $fromDt->setTime(intval($startTimeArray[0]), intval($startTimeArray[1]));

                    $toTimeArray = explode(':', $toDtTime);
                    $toDt->setTime(intval($toTimeArray[0]), intval($toTimeArray[1]));
                }

                // What if the fromdt and todt are exactly the same?
                // in this case assume an entire day from midnight on the fromdt to midnight on the todt (i.e. add a day to the todt)
                if ($fromDt == $toDt) {
                    $toDt->addDay();
                }

                break;
        }

        //
        // Get Results!
        // -------------
        $timeSeriesStore = $this->getTimeSeriesStore()->getEngine();
        if ($timeSeriesStore == 'mongodb') {
            $result = $this->getProofOfPlayReportMongoDb(
                $fromDt,
                $toDt,
                $displayIds,
                $parentCampaignId,
                $layoutIds,
                $mediaIds,
                $type,
                $columns,
                $tags,
                $tagsType,
                $exactTags,
                $operator,
                $groupBy,
                $displayGroupIds
            );
        } else {
            $result = $this->getProofOfPlayReportMySql(
                $fromDt,
                $toDt,
                $displayIds,
                $parentCampaignId,
                $layoutIds,
                $mediaIds,
                $type,
                $columns,
                $tags,
                $tagsType,
                $exactTags,
                $operator,
                $groupBy
            );
        }

        // Sanitize results
        $rows = [];
        foreach ($result['result'] as $row) {
            $entry = [];
            $sanitizedRow = $this->sanitizer->getSanitizer($row);

            $widgetId = $sanitizedRow->getInt('widgetId');
            $widgetName = $sanitizedRow->getString('media');
            // If the media name is empty, and the widgetid is not, then we can assume it has been deleted.
            $widgetName = ($widgetName == '' &&  $widgetId != 0) ? __('Deleted from Layout') : $widgetName;
            $displayName = $sanitizedRow->getString('display');
            $layoutName = $sanitizedRow->getString('layout');
            $parentCampaignName = $sanitizedRow->getString('parentCampaign');

            $entry['type'] = $sanitizedRow->getString('type');
            $entry['displayId'] = $sanitizedRow->getInt('displayId');
            $entry['display'] = ($displayName != '') ? $displayName : __('Not Found');
            $entry['layoutId'] = $sanitizedRow->getInt('layoutId');
            $entry['layout'] = ($layoutName != '') ? $layoutName :  __('Not Found');
            $entry['parentCampaignId'] = $sanitizedRow->getInt('parentCampaignId');
            $entry['parentCampaign'] = $parentCampaignName;
            $entry['widgetId'] = $sanitizedRow->getInt('widgetId');
            $entry['media'] = $widgetName;
            $entry['tag'] = $sanitizedRow->getString('tag');
            $entry['numberPlays'] = $sanitizedRow->getInt('numberPlays');
            $entry['duration'] = $sanitizedRow->getInt('duration');
            $entry['minStart'] = Carbon::createFromTimestamp($row['minStart'])->format(DateFormatHelper::getSystemFormat());
            $entry['maxEnd'] =  Carbon::createFromTimestamp($row['maxEnd'])->format(DateFormatHelper::getSystemFormat());
            $entry['mediaId'] = $sanitizedRow->getInt('mediaId');
            $entry['displayGroup'] = $sanitizedRow->getString('displayGroup');
            $entry['displayGroupId'] = $sanitizedRow->getInt('displayGroupId');
            $entry['tagName'] = $sanitizedRow->getString('tagName');
            $entry['tagId'] = $sanitizedRow->getInt('tagId');
            $rows[] = $entry;
        }

        // Set Meta data
        $metadata = [
            'periodStart' => $result['periodStart'],
            'periodEnd' => $result['periodEnd'],
        ];

        $recordsTotal = $result['count'];

        // ----
        // Table Only
        // Return data to build chart/table
        // This will get saved to a json file when schedule runs
        return new ReportResult(
            $metadata,
            $rows,
            $recordsTotal
        );
    }

    /**
     * MySQL proof of play report
     * @param Carbon $fromDt The filter range from date
     * @param Carbon $toDt The filter range to date
     * @param $displayIds array
     * @param $parentCampaignId int
     * @param $layoutIds array
     * @param $mediaIds array
     * @param $type string
     * @param $columns array
     * @param $tags string
     * @param $tagsType string
     * @param $exactTags mixed
     * @param $groupBy string
     * @return array[array result, date periodStart, date periodEnd, int count, int totalStats]
     */
    private function getProofOfPlayReportMySql(
        $fromDt,
        $toDt,
        $displayIds,
        $parentCampaignId,
        $layoutIds,
        $mediaIds,
        $type,
        $columns,
        $tags,
        $tagsType,
        $exactTags,
        $logicalOperator,
        $groupBy
    ) {
        $fromDt = $fromDt->format('U');
        $toDt = $toDt->format('U');

        // Media on Layouts Ran
        $select = '
          SELECT stat.type,
              stat.parentCampaignId,
              campaign.campaign as parentCampaign,
              IFNULL(layout.Layout, 
                  (SELECT MAX(`layout`) AS layout 
                     FROM `layout` 
                        INNER JOIN `layouthistory`
                        ON `layout`.layoutId = `layouthistory`.layoutId
                    WHERE `layouthistory`.campaignId = `stat`.campaignId)
              ) AS Layout,
              IFNULL(`media`.name, IFNULL(`widgetoption`.value, `widget`.type)) AS Media,
              SUM(stat.count) AS NumberPlays,
              SUM(stat.duration) AS Duration,
              MIN(start) AS MinStart,
              MAX(end) AS MaxEnd,
              stat.tag,
              stat.layoutId,
              stat.mediaId,
              stat.widgetId
        ';

        // We get the ID and name - either by display, display group or tag
        if ($groupBy === 'display') {
            $select .= ', display.Display, stat.displayId ';
        } else if ($groupBy === 'displayGroup') {
            $select .= ', displaydg.displayGroup, displaydg.displayGroupId ';
        } else if ($groupBy === 'tag') {
            if ($tagsType === 'dg' || $tagsType === 'media') {
                $select .= ', taglink.value, taglink.tagId ';
            } else {
                // For layouts, we need to manually select taglink.tag
                $select .= ', taglink.tag AS value, taglink.tagId ';
            }
        }

        $body = '
            FROM stat
              LEFT OUTER JOIN display
              ON stat.DisplayID = display.DisplayID
              LEFT OUTER JOIN layouthistory 
              ON layouthistory.LayoutID = stat.LayoutID              
              LEFT OUTER JOIN layout
              ON layout.LayoutID = layouthistory.layoutId
              LEFT OUTER JOIN `widget`
              ON `widget`.widgetId = stat.widgetId
              LEFT OUTER JOIN `widgetoption`
              ON `widgetoption`.widgetId = `widget`.widgetId
                AND `widgetoption`.type = \'attrib\'
                AND `widgetoption`.option = \'name\'
              LEFT OUTER JOIN `media`
              ON `media`.mediaId = `stat`.mediaId
              LEFT OUTER JOIN `campaign`
              ON `campaign`.campaignId = `stat`.parentCampaignId
              ';

        if ($tags != '') {
            if ($tagsType === 'dg') {
                $body .= 'INNER JOIN `lkdisplaydg`
                        ON lkdisplaydg.DisplayID = display.displayid
                     INNER JOIN `displaygroup`
                        ON displaygroup.displaygroupId = lkdisplaydg.displaygroupId
                         AND `displaygroup`.isDisplaySpecific = 1 ';
            }
        }

        if ($groupBy === 'displayGroup') {
            // Group the data by display group
            $body .= 'INNER JOIN `lkdisplaydg` AS linkdg
                        ON linkdg.DisplayID = display.displayid
                     INNER JOIN `displaygroup` AS displaydg
                        ON displaydg.displaygroupId = linkdg.displaygroupId
                         AND `displaydg`.isDisplaySpecific = 0 ';
        } else if ($groupBy === 'tag') {
            $body .= $this->groupByTagType($tagsType);
        }

        $body .= ' WHERE stat.type <> \'displaydown\'
                AND stat.end > :fromDt
                AND stat.start < :toDt
        ';

        // Filter by display
        if (count($displayIds) > 0) {
            $body .= ' AND stat.displayID IN (' . implode(',', $displayIds) . ') ';
        }

        $params = [
            'fromDt' => $fromDt,
            'toDt' => $toDt
        ];

        if ($tags != '') {
            if (trim($tags) === '--no-tag') {
                if ($tagsType === 'dg') {
                    $body .= ' AND `displaygroup`.displaygroupId NOT IN (
                    SELECT `lktagdisplaygroup`.displaygroupId
                     FROM tag
                        INNER JOIN `lktagdisplaygroup`
                        ON `lktagdisplaygroup`.tagId = tag.tagId
                        )
                        ';
                }

                // old layout and latest layout have same tags
                // old layoutId replaced with latest layoutId in the lktaglayout table and
                // join with layout history to get campaignId then we can show old layouts that have no tag
                if ($tagsType === 'layout') {
                    $body .= ' AND `stat`.campaignId NOT IN (
                        SELECT 
                            `layouthistory`.campaignId
                        FROM
                        (
                            SELECT `lktaglayout`.layoutId
                            FROM tag
                            INNER JOIN `lktaglayout`
                            ON `lktaglayout`.tagId = tag.tagId ) B
                        LEFT OUTER JOIN
                        `layouthistory` ON `layouthistory`.layoutId = B.layoutId 
                        )
                        ';
                }
                if ($tagsType === 'media') {
                    $body .= ' AND `media`.mediaId NOT IN (
                    SELECT `lktagmedia`.mediaId
                     FROM tag
                        INNER JOIN `lktagmedia`
                        ON `lktagmedia`.tagId = tag.tagId
                        )
                        ';
                }
            } else {
                $operator = $exactTags == 1 ? '=' : 'LIKE';
                $lkTagTable = '';
                $lkTagTableIdColumn = '';
                $idColumn = '';
                $allTags = explode(',', $tags);
                $excludeTags = [];
                $includeTags = [];

                foreach ($allTags as $tag) {
                    if (str_starts_with($tag, '-')) {
                        $excludeTags[] = ltrim(($tag), '-');
                    } else {
                        $includeTags[] = $tag;
                    }
                }

                if ($tagsType === 'dg') {
                    $lkTagTable = 'lktagdisplaygroup';
                    $lkTagTableIdColumn = 'lkTagDisplayGroupId';
                    $idColumn = 'displayGroupId';
                }

                if ($tagsType === 'layout') {
                    $lkTagTable = 'lktaglayout';
                    $lkTagTableIdColumn = 'lkTagLayoutId';
                    $idColumn = 'layoutId';
                }
                if ($tagsType === 'media') {
                    $lkTagTable = 'lktagmedia';
                    $lkTagTableIdColumn = 'lkTagMediaId';
                    $idColumn = 'mediaId';
                }

                if (!empty($excludeTags)) {
                    $body .= $this->getBodyForTagsType($tagsType, true);
                    // pass to BaseFactory tagFilter, it does not matter from which factory we do that.
                    $this->layoutFactory->tagFilter(
                        $excludeTags,
                        $lkTagTable,
                        $lkTagTableIdColumn,
                        $idColumn,
                        $logicalOperator,
                        $operator,
                        true,
                        $body,
                        $params
                    );

                    // old layout and latest layout have same tags
                    // old layoutId replaced with latest layoutId in the lktaglayout table and
                    // join with layout history to get campaignId then we can show old layouts that have given tag
                    if ($tagsType === 'layout') {
                        $body .= ' B
                        LEFT OUTER JOIN
                        `layouthistory` ON `layouthistory`.layoutId = B.layoutId ) ';
                    }
                }

                if (!empty($includeTags)) {
                    $body .= $this->getBodyForTagsType($tagsType, false);
                    // pass to BaseFactory tagFilter, it does not matter from which factory we do that.
                    $this->layoutFactory->tagFilter(
                        $includeTags,
                        $lkTagTable,
                        $lkTagTableIdColumn,
                        $idColumn,
                        $logicalOperator,
                        $operator,
                        false,
                        $body,
                        $params
                    );

                    // old layout and latest layout have same tags
                    // old layoutId replaced with latest layoutId in the lktaglayout table and
                    // join with layout history to get campaignId then we can show old layouts that have given tag
                    if ($tagsType === 'layout') {
                        $body .= ' C
                        LEFT OUTER JOIN
                        `layouthistory` ON `layouthistory`.layoutId = C.layoutId ) ';
                    }
                }
            }
        }

        // Type filter
        if ($type == 'layout') {
            $body .= ' AND `stat`.type = \'layout\' ';
        } elseif ($type == 'media') {
            $body .= ' AND `stat`.type = \'media\' AND IFNULL(`media`.mediaId, 0) <> 0 ';
        } elseif ($type == 'widget') {
            $body .= ' AND `stat`.type = \'widget\' AND IFNULL(`widget`.widgetId, 0) <> 0 ';
        } elseif ($type == 'event') {
            $body .= ' AND `stat`.type = \'event\' ';
        }

        // Campaign Filter
        if ($parentCampaignId != null) {
            $body .= ' AND `stat`.parentCampaignId = :parentCampaignId ';
            $params['parentCampaignId'] = $parentCampaignId;
        }

        // Layout Filter
        if (count($layoutIds) != 0) {
            $layoutSql = '';
            $i = 0;
            foreach ($layoutIds as $layoutId) {
                $i++;
                $layoutSql .= ':layoutId_' . $i . ',';
                $params['layoutId_' . $i] = $layoutId;
            }

            $body .= '  AND `stat`.campaignId IN (SELECT campaignId from layouthistory where layoutId IN ('
                . trim($layoutSql, ',') . ')) ';
        }

        // Media Filter
        if (count($mediaIds) != 0) {
            $mediaSql = '';
            $i = 0;
            foreach ($mediaIds as $mediaId) {
                $i++;
                $mediaSql .= ':mediaId_' . $i . ',';
                $params['mediaId_' . $i] = $mediaId;
            }

            $body .= ' AND `media`.mediaId IN (' . trim($mediaSql, ',') . ')';
        }

        // We first implement default groupings
        $body .= '
            GROUP BY stat.type, 
                stat.tag, 
                stat.parentCampaignId,
                stat.campaignId,
                layout.layout, 
                IFNULL(stat.mediaId, stat.widgetId), 
                IFNULL(`media`.name, IFNULL(`widgetoption`.value, `widget`.type)),
                stat.layoutId,
                stat.mediaId,
                stat.widgetId
        ';

        // Then add the optional groupings
        if ($groupBy === 'display') {
            $body .= ', display.Display, stat.displayId';
        } else if ($groupBy === 'displayGroup') {
            $body .= ', displaydg.displayGroupId, displaydg.displayGroup';
        } else if ($groupBy === 'tag') {
            $body .= ', value, taglink.tagId';
        }

        $order = '';
        if ($columns != null) {
            $order = 'ORDER BY ' . implode(',', $columns);
        }

        /*Execute sql statement*/
        $sql = $select . $body . $order;

        $rows = [];
        foreach ($this->store->select($sql, $params) as $row) {
            $entry = [];

            $entry['type'] = $row['type'];
            $entry['displayId'] = $row['displayId'] ?? '';
            $entry['display'] = $row['Display'] ?? '';
            $entry['layout'] = $row['Layout'];
            $entry['parentCampaignId'] = $row['parentCampaignId'];
            $entry['parentCampaign'] = $row['parentCampaign'];
            $entry['media'] = $row['Media'];
            $entry['numberPlays'] = $row['NumberPlays'];
            $entry['duration'] = $row['Duration'];
            $entry['minStart'] = $row['MinStart'];
            $entry['maxEnd'] = $row['MaxEnd'];
            $entry['layoutId'] = $row['layoutId'];
            $entry['widgetId'] = $row['widgetId'];
            $entry['mediaId'] = $row['mediaId'];
            $entry['tag'] = $row['tag'];
            $entry['displayGroupId'] = $row['displayGroupId'] ?? '';
            $entry['displayGroup'] = $row['displayGroup'] ?? '';
            $entry['tagId'] = $row['tagId'] ?? '';
            $entry['tagName'] = $row['value'] ?? '';
            $rows[] = $entry;
        }

        return [
            'periodStart' => Carbon::createFromTimestamp($fromDt)->format(DateFormatHelper::getSystemFormat()),
            'periodEnd' => Carbon::createFromTimestamp($toDt)->format(DateFormatHelper::getSystemFormat()),
            'result' => $rows,
            'count' => count($rows)
        ];
    }

    private function getBodyForTagsType($tagsType, $exclude) :string
    {
        if ($tagsType === 'dg') {
            return ' AND `displaygroup`.displaygroupId ' . ($exclude ? 'NOT' : '') .  ' IN (
                        SELECT `lktagdisplaygroup`.displaygroupId
                          FROM tag
                            INNER JOIN `lktagdisplaygroup`
                            ON `lktagdisplaygroup`.tagId = tag.tagId
                ';
        } else if ($tagsType === 'media') {
            return ' AND `media`.mediaId '. ($exclude ? 'NOT' : '') . ' IN (
                        SELECT `lktagmedia`.mediaId
                          FROM tag
                            INNER JOIN `lktagmedia`
                            ON `lktagmedia`.tagId = tag.tagId
                ';
        } else if ($tagsType === 'layout') {
            return ' AND `stat`.campaignId ' . ($exclude ? 'NOT' : '') . ' IN (
                        SELECT 
                            `layouthistory`.campaignId
                        FROM
                        (
                            SELECT `lktaglayout`.layoutId
                            FROM tag
                            INNER JOIN `lktaglayout`
                            ON `lktaglayout`.tagId = tag.tagId
                        ';
        } else {
            $this->getLog()->error(__('Incorrect Tag type selected'));
            return '';
        }
    }

    /**
     * MongoDB proof of play report
     * @param Carbon $filterFromDt The filter range from date
     * @param Carbon $filterToDt The filter range to date
     * @param $displayIds array
     * @param $parentCampaignId int
     * @param $layoutIds array
     * @param $mediaIds array
     * @param $type string
     * @param $columns array
     * @param $tags string
     * @param $tagsType string
     * @param $exactTags mixed
     * @param $groupBy string
     * @param $displayGroupIds array
     * @return array[array result, date periodStart, date periodEnd, int count, int totalStats]
     * @throws InvalidArgumentException
     * @throws \Xibo\Support\Exception\GeneralException
     */
    private function getProofOfPlayReportMongoDb(
        $filterFromDt,
        $filterToDt,
        $displayIds,
        $parentCampaignId,
        $layoutIds,
        $mediaIds,
        $type,
        $columns,
        $tags,
        $tagsType,
        $exactTags,
        $operator,
        $groupBy,
        $displayGroupIds
    ) {
        $fromDt = new UTCDateTime($filterFromDt->format('U')*1000);
        $toDt = new UTCDateTime($filterToDt->format('U')*1000);

        // Filters the documents to pass only the documents that
        // match the specified condition(s) to the next pipeline stage.
        $match =  [
            '$match' => [
                'end' => ['$gt' => $fromDt],
                'start' => ['$lt' => $toDt]
            ]
        ];

        // Display Filter
        if (count($displayIds) > 0) {
            $match['$match']['displayId'] = [ '$in' => $displayIds ];
        }

        // Type Filter
        if ($type != null) {
            $match['$match']['type'] = $type;
        }

        $tagsArray = [];

        // Tag Filter
        if ($tags != null) {
            $i = 0;
            foreach (explode(',', $tags) as $tag) {
                $tagV = explode('|', $tag);

                if (!isset($tagV[1])) {
                    $tagsArray[$i]['tag'] = $tag;
                } elseif ($tagV[0] == '') {
                    $tagsArray[$i]['val'] = $tagV[1];
                } else {
                    $tagsArray[$i]['tag'] = $tagV[0];
                    $tagsArray[$i]['val'] = $tagV[1];
                }
                $i++;
            }

            if ($exactTags != 1) {
                $tagsArray = array_map(function ($tagValue) {
                    return array_map(function ($tag) {
                        return new \MongoDB\BSON\Regex('.*'.$tag. '.*', 'i');
                    }, $tagValue);
                }, $tagsArray);
            }

            // When exact match is not desired
            if (count($tagsArray) > 0) {
                $logicalOperator = ($operator === 'AND') ? '$and' : '$or';
                foreach ($tagsArray as $tag) {
                    $match['$match'][$logicalOperator][] = [
                        'tagFilter.' . $tagsType => [
                            '$elemMatch' => $tag
                        ]
                    ];
                }
            }
        }

        // Campaign Filter
        if ($parentCampaignId != null) {
            $match['$match']['parentCampaignId'] = $parentCampaignId;
        }

        // Layout Filter
        if (count($layoutIds) != 0) {
            // Get campaignIds for selected layoutIds
            $campaignIds = [];
            foreach ($layoutIds as $layoutId) {
                try {
                    $campaignIds[] = $this->layoutFactory->getCampaignIdFromLayoutHistory($layoutId);
                } catch (NotFoundException $notFoundException) {
                    // Ignore the missing one
                    $this->getLog()->debug('Filter for Layout without Layout History Record, layoutId is ' . $layoutId);
                }
            }
            $match['$match']['campaignId'] = [ '$in' => $campaignIds ];
        }

        // Media Filter
        if (count($mediaIds) != 0) {
            $this->getLog()->debug(json_encode($mediaIds, JSON_PRETTY_PRINT));
            $match['$match']['mediaId'] = [ '$in' => $mediaIds ];
        }

        // For sorting
        // The selected column has a key
        $temp = [
            '_id.type' => 'type',
            '_id.display' => 'display',
            'layout' => 'layout',
            'media' => 'media',
            'eventName' => 'eventName',
            'layoutId' => 'layoutId',
            'parentCampaignId' => 'parentCampaignId',
            'parentCampaign' => 'parentCampaign',
            'widgetId' => 'widgetId',
            '_id.displayId' => 'displayId',
            'numberPlays' => 'numberPlays',
            'minStart' => 'minStart',
            'maxEnd' => 'maxEnd',
            'duration' => 'duration',
        ];

        // Remove ` and DESC from the array strings
        $cols = [];
        foreach ($columns as $column) {
            $str = str_replace('`', '', str_replace(' DESC', '', $column));
            if (\strpos($column, 'DESC') !== false) {
                $cols[$str] = -1;
            } else {
                $cols[$str] = 1;
            }
        }

        // The selected column key gets stored in an array with value 1 or -1 (for DESC)
        $array = [];
        foreach ($cols as $k => $v) {
            if (array_search($k, $temp)) {
                $array[array_search($k, $temp)] = $v;
            }
        }

        $order = ['_id.type'=> 1]; // default sorting by type
        if ($array != null) {
            $order = $array;
        }

        $project = [
            '$project' => [
                'campaignId' =>  1,
                'mediaId' =>  1,
                'mediaName'=> 1,
                'media'=> [ '$ifNull' => [ '$mediaName', '$widgetName' ] ],
                'eventName' => 1,
                'widgetId' =>  1,
                'widgetName' =>  1,
                'layoutId' =>  1,
                'layoutName' =>  1,
                'parentCampaignId' =>  1,
                'parentCampaign' =>  1,
                'displayId' =>  1,
                'displayName' =>  1,
                'start' => 1,
                'end' => 1,
                'type' => 1,
                'duration' => 1,
                'count' => 1,
                'total' => ['$sum' => 1],
            ]
        ];

        $group = [
            '$group' => [
                '_id' => [
                    'type' => '$type',
                    'parentCampaignId'=> '$parentCampaignId',
                    'campaignId'=> [ '$ifNull' => [ '$campaignId', '$layoutId' ] ],
                    'mediaorwidget'=> [ '$ifNull' => [ '$mediaId', '$widgetId' ] ],
                    'displayId'=> [ '$ifNull' => [ '$displayId', null ] ],
                    'display'=> '$displayName',
                    'eventName'=> '$eventName',
                    // we don't need to group by media name and widget name

                ],

                'media'=> [ '$first' => '$media'],
                'eventName'=> [ '$first' => '$eventName'],
                'mediaId' => ['$first' => '$mediaId'],
                'widgetId' => ['$first' => '$widgetId' ],

                'layout' => ['$first' => '$layoutName'],

                // use the last layoutId to say that is the latest layoutId
                'layoutId' => ['$last' => '$layoutId'],

                'parentCampaign' => ['$first' => '$parentCampaign'],
                'parentCampaignId' => ['$first' => '$parentCampaignId'],

                'minStart' => ['$min' => '$start'],
                'maxEnd' => ['$max' => '$end'],
                'numberPlays' => ['$sum' => '$count'],
                'duration' => ['$sum' => '$duration'],
                'total' => ['$max' => '$total'],
            ],
        ];

        $query = [
            $match,
            $project,
            $group, [
                '$facet' => [
                    'totalData' => [
                        ['$sort' => $order],
                    ]
                ]
            ],

        ];

        $result = $this->getTimeSeriesStore()->executeQuery(['collection' => $this->table, 'query' => $query]);

        $rows = [];
        if (count($result) > 0) {
            // Grid results
            foreach ($result[0]['totalData'] as $row) {
                $entry = [];

                $entry['type'] = $row['_id']['type'];
                $entry['displayId'] = $row['_id']['displayId'];
                $entry['display'] = isset($row['_id']['display']) ? $row['_id']['display']: 'No display';
                $entry['layout'] = isset($row['layout']) ? $row['layout']: 'No layout';
                $entry['parentCampaignId'] = isset($row['parentCampaignId']) ? $row['parentCampaignId']: '';
                $entry['parentCampaign'] = isset($row['parentCampaign']) ? $row['parentCampaign']: '';
                $entry['media'] = isset($row['media']) ? $row['media'] : 'No media' ;
                $entry['numberPlays'] = $row['numberPlays'];
                $entry['duration'] = $row['duration'];
                $entry['minStart'] = $row['minStart']->toDateTime()->format('U');
                $entry['maxEnd'] = $row['maxEnd']->toDateTime()->format('U');
                $entry['layoutId'] = $row['layoutId'];
                $entry['widgetId'] = $row['widgetId'];
                $entry['mediaId'] = $row['mediaId'];
                $entry['tag'] = $row['eventName'];
                $entry['displayGroupId'] = '';
                $entry['displayGroup'] = '';
                $entry['tagId'] = '';
                $entry['tagName'] = '';

                $rows[] = $entry;
            }
        }

        if ($groupBy === 'tag') {
            $rows = $this->groupByTagMongoDb($rows, $tagsType);
        } else if ($groupBy === 'displayGroup') {
            $rows = $this->groupByDisplayGroupMongoDb($rows, $displayGroupIds);
        }

        return [
            'periodStart' => $filterFromDt->format(DateFormatHelper::getSystemFormat()),
            'periodEnd' => $filterToDt->format(DateFormatHelper::getSystemFormat()),
            'result' => $rows,
            'count' => count($rows)
        ];
    }

    /**
     * Add grouping by tag type
     * @param string $tagType
     * @return string
     */
    private function groupByTagType(string $tagType) : string
    {
        return match ($tagType) {
            'media' => 'INNER JOIN `lktagmedia` AS taglink ON taglink.mediaId = stat.mediaId',
            'layout' => 'INNER JOIN `lktaglayout` ON `lktaglayout`.layoutId = stat.layoutId
                         INNER JOIN `tag` AS taglink ON taglink.tagId = `lktaglayout`.tagId',
            'dg' => 'INNER JOIN `lkdisplaydg` AS linkdg
                        ON linkdg.DisplayID = display.displayid
                     INNER JOIN `displaygroup` AS displaydg
                        ON displaydg.displaygroupId = linkdg.displaygroupId
                         AND `displaydg`.isDisplaySpecific = 1 INNER JOIN
                         `lktagdisplaygroup` AS taglink ON taglink.displaygroupId = displaydg.displaygroupId',
        };
    }

    /**
     * Group by display group in MongoDB
     * @param array $rows
     * @param array $filteredDisplayGroupIds
     * @return array
     * @throws NotFoundException
     */
    private function groupByDisplayGroupMongoDb(array $rows, array $filteredDisplayGroupIds) : array
    {
        $data = [];
        $displayInfoArr = $this->displayGroupFactory->query();

        // Get the display groups
        foreach ($rows as $row) {
            foreach ($displayInfoArr as $dg) {
                // Do we have a filter?
                if (!$filteredDisplayGroupIds || in_array($dg->displayGroupId, $filteredDisplayGroupIds)) {
                    // Create a temporary key to group by multiple columns at once
                    // and save memory instead of checking each column recursively
                    $key = $dg->displayGroupId . '_' . $row['layoutId'] . '_' . $row['mediaId'] . '_' .
                        $row['tag'] . '_' . $row['widgetId'] . '_' . $row['parentCampaignId'] . '_' . $row['type'];

                    if (!isset($data[$key])) {
                        // Since we already have the display group as the grouping option, we can remove the display info
                        $row['display'] = null;
                        $row['displayId'] = null;
                        $row['displayGroupId'] = $dg->displayGroupId;
                        $row['displayGroup'] = $dg->displayGroup;

                        $data[$key] = $row;
                    } else {
                        $data[$key]['duration'] += $row['duration'];
                        $data[$key]['numberPlays'] += $row['numberPlays'];
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Group by tag in MongoDB
     * @param array $rows
     * @param string $tagsType
     * @return array
     */
    private function groupByTagMongoDb(array $rows, string $tagsType) : array
    {
        $data = [];
        $tags = $this->filterByTagType($tagsType);
        $type = match ($tagsType) {
            'media' => 'mediaId',
            'layout' => 'layoutId',
            'dg' => 'displayId',
        };;

        foreach ($rows as $row) {
            foreach ($tags as $tag) {
                if ($row[$type] == $tag['entityId']) {
                    // Create a temporary key to group by multiple columns at once
                    // and save memory instead of checking each column recursively
                    $key = $tag['tagId'] . '_' . $row['layoutId'] . '_' . $row['mediaId'] . '_' .
                        $row['tag'] . '_' . $row['widgetId'] . '_' . $row['parentCampaignId'] . '_' . $row['type'];

                    if (!isset($data[$key])) {
                        // Since we already have the tags as the grouping option, we can remove the display info
                        $row['display'] = null;
                        $row['displayId'] = null;
                        $row['tagName'] = $tag['tag'];
                        $row['tagId'] = $tag['tagId'];

                        $data[$key] = $row;
                    } else {
                        $data[$key]['duration'] += $row['duration'];
                        $data[$key]['numberPlays'] += $row['numberPlays'];
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param string $tagsType
     * @return array
     */
    private function filterByTagType(string $tagsType): array
    {
        $tags = [];
        $filter = match ($tagsType) {
            'media' => 'Media',
            'layout' => 'Layout',
            'dg' => 'Display',
        };

        // Get the list of tags to get the tag type (ie media tag, layout tag, or display tag)
        $tagInfoArr = $this->tagFactory->query();

        foreach ($tagInfoArr as $tag) {
            // What type of tags are we looking for?
            foreach ($this->tagFactory->getAllLinks(null, ['tagId' => $tag->tagId]) as $filteredTag) {
                if ($filteredTag['type'] == $filter) {
                    $filteredTag['tagId'] = $tag->tagId;
                    $filteredTag['tag'] = $tag->tag;
                    $tags[] = $filteredTag;
                }
            }
        }
        
        return $tags;
    }
}
