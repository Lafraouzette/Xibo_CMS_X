<?php


namespace Xibo\Event;

/**
 * Event raised when a Layout's sharing has been changed.
 */
class LayoutSharingChangeEvent extends Event
{
    public static string $NAME = 'layout.sharing.change.event';

    /** @var int[] */
    private array $canvasRegionIds;

    /**
     * LayoutSharingChangeEvent constructor.
     * @param int $campaignId
     */
    public function __construct(private readonly int $campaignId)
    {
        $this->canvasRegionIds = [];
    }

    /**
     * @return int
     */
    public function getCampaignId(): int
    {
        return $this->campaignId;
    }

    /**
     * Get the Canvas Region ID
     * @return int[]
     */
    public function getCanvasRegionIds(): array
    {
        return $this->canvasRegionIds;
    }

    /**
     * Set the Canvas Region ID
     */
    public function addCanvasRegionId(int $regionId): void
    {
        $this->canvasRegionIds[] = $regionId;
    }
}
