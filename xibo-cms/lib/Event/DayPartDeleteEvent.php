<?php

namespace Xibo\Event;

use Xibo\Entity\DayPart;

class DayPartDeleteEvent extends Event
{
    public static $NAME = 'dayPart.delete.event';

    private $dayPart;

    public function __construct(DayPart $dayPart)
    {
        $this->dayPart = $dayPart;
    }

    public function getDayPart(): DayPart
    {
        return $this->dayPart;
    }
}
