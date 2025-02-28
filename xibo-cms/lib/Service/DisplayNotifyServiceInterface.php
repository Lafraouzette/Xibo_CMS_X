<?php



namespace Xibo\Service;

use Stash\Interfaces\PoolInterface;
use Xibo\Entity\Display;
use Xibo\Factory\ScheduleFactory;
use Xibo\Storage\StorageServiceInterface;

/**
 * Interface DisplayNotifyServiceInterface
 * @package Xibo\Service
 */
interface DisplayNotifyServiceInterface
{
    /**
     * DisplayNotifyServiceInterface constructor.
     * @param ConfigServiceInterface $config
     * @param StorageServiceInterface $store
     * @param LogServiceInterface $log
     * @param PoolInterface $pool
     * @param PlayerActionServiceInterface $playerActionService
     * @param ScheduleFactory $scheduleFactory
     */
    public function __construct($config, $store, $log, $pool, $playerActionService, $scheduleFactory);

    /**
     * Initialise
     * @return $this
     */
    public function init();

    /**
     * @return $this
     */
    public function collectNow();

    /**
     * @return $this
     */
    public function collectLater();

    /**
     * Process Queue of Display Notifications
     * @return $this
     */
    public function processQueue();

    /**
     * Notify by Display Id
     * @param $displayId
     */
    public function notifyByDisplayId($displayId);

    /**
     * Notify by Display Group Id
     * @param $displayGroupId
     */
    public function notifyByDisplayGroupId($displayGroupId);

    /**
     * Notify by CampaignId
     * @param $campaignId
     */
    public function notifyByCampaignId($campaignId);

    /**
     * Notify by DataSetId
     * @param $dataSetId
     */
    public function notifyByDataSetId($dataSetId);

    /**
     * Notify by PlaylistId
     * @param $playlistId
     */
    public function notifyByPlaylistId($playlistId);

    /**
     * Notify By Layout Code
     * @param $code
     */
    public function notifyByLayoutCode($code);

    /**
     * Notify by Menu Board ID
     * @param $menuId
     */
    public function notifyByMenuBoardId($menuId);

    /**
     * Notify that data has been updated for this display
     * @param \Xibo\Entity\Display $display
     * @param int $widgetId
     * @return void
     */
    public function notifyDataUpdate(Display $display, int $widgetId): void;
}
