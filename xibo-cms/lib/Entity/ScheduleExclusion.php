<?php

namespace Xibo\Entity;
use Xibo\Service\LogServiceInterface;
use Xibo\Storage\StorageServiceInterface;


/**
 * Class ScheduleExclusion
 * @package Xibo\Entity
 *
 * @SWG\Definition()
 */
class ScheduleExclusion implements \JsonSerializable
{
    use EntityTrait;

    /**
     * @SWG\Property(description="Excluded Schedule ID")
     * @var int
     */
    public $scheduleExclusionId;

    /**
     * @SWG\Property(description="The eventId that this Excluded Schedule applies to")
     * @var int
     */
    public $eventId;

    /**
     * @SWG\Property(
     *  description="A Unix timestamp representing the from date of an excluded recurring event in CMS time."
     * )
     * @var int
     */
    public $fromDt;

    /**
     * @SWG\Property(
     *  description="A Unix timestamp representing the to date of an excluded recurring event in CMS time."
     * )
     * @var int
     */
    public $toDt;

    /**
     * Entity constructor.
     * @param StorageServiceInterface $store
     * @param LogServiceInterface $log
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct($store, $log, $dispatcher)
    {
        $this->setCommonDependencies($store, $log, $dispatcher);
    }

    public function save()
    {
        $this->getStore()->insert('INSERT INTO `scheduleexclusions` (`eventId`, `fromDt`, `toDt`) VALUES (:eventId, :fromDt, :toDt)', [
            'eventId' => $this->eventId,
            'fromDt' => $this->fromDt,
            'toDt' => $this->toDt,
        ]);
    }

    public function delete()
    {
        $this->getStore()->update('DELETE FROM `scheduleexclusions` WHERE `scheduleExclusionId` = :scheduleExclusionId', [
            'scheduleExclusionId' => $this->scheduleExclusionId
        ]);
    }
}