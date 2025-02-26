<?php


namespace Xibo\Factory;

use Xibo\Entity\ScheduleExclusion;

/**
 * Class ScheduleExclusionFactory
 * @package Xibo\Factory
 */
class ScheduleExclusionFactory extends BaseFactory
{
    /**
     * Load by Event Id
     * @param int $eventId
     * @return array[ScheduleExclusion]
     */
    public function getByEventId($eventId)
    {
        return $this->query(null, array('eventId' => $eventId));
    }

    /**
     * Create Empty
     * @return ScheduleExclusion
     */
    public function createEmpty()
    {
        return new ScheduleExclusion($this->getStore(), $this->getLog(), $this->getDispatcher());
    }

    /**
     * Create a schedule exclusion
     * @param int $eventId
     * @param int $fromDt
     * @param int $toDt
     * @return ScheduleExclusion
     */
    public function create($eventId, $fromDt, $toDt)
    {
        $scheduleExclusion = $this->createEmpty();
        $scheduleExclusion->eventId = $eventId;
        $scheduleExclusion->fromDt = $fromDt;
        $scheduleExclusion->toDt = $toDt;

        return $scheduleExclusion;
    }

    /**
     * Query Schedule exclusions
     * @param array $sortOrder
     * @param array $filterBy
     * @return array[ScheduleExclusion]
     */
    public function query($sortOrder = null, $filterBy = [])
    {
        $entries = [];
        $sanitizedFilter = $this->getSanitizer($filterBy);

        $sql = 'SELECT * FROM `scheduleexclusions` WHERE eventId = :eventId';

        foreach ($this->getStore()->select($sql, array('eventId' => $sanitizedFilter->getInt('eventId'))) as $row) {
            $entries[] = $this->createEmpty()->hydrate($row);
        }

        return $entries;
    }
}