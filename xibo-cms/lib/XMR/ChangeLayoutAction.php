<?php


namespace Xibo\XMR;

class ChangeLayoutAction extends PlayerAction
{
    public $layoutId;
    public $duration;
    public $downloadRequired;
    public $changeMode;

    public function __construct()
    {
        $this->setQos(10);
    }

    /**
     * Set details for this layout
     * @param int $layoutId the layoutId to change to
     * @param int $duration the duration this layout should be shown
     * @param bool|false $downloadRequired flag indicating whether a download is required before changing to the layout
     * @param string $changeMode whether to queue or replace
     * @return $this
     */
    public function setLayoutDetails($layoutId, $duration = 0, $downloadRequired = false, $changeMode = 'queue')
    {
        if ($duration === null) {
            $duration = 0;
        }

        $this->layoutId = $layoutId;
        $this->duration = $duration;
        $this->downloadRequired = $downloadRequired;
        $this->changeMode = $changeMode;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        $this->action = 'changeLayout';

        if ($this->layoutId == 0) {
            throw new PlayerActionException('Layout Details not provided');
        }

        return $this->serializeToJson(['layoutId', 'duration', 'downloadRequired', 'changeMode']);
    }
}
