<?php


namespace Xibo\Event;

class TagDeleteEvent extends Event
{
    public static $NAME = 'tag.delete.event';
    /**
     * @var int
     */
    private $tagId;

    public function __construct(int $tagId)
    {
        $this->tagId = $tagId;
    }

    /**
     * @return int
     */
    public function getTagId(): int
    {
        return $this->tagId;
    }
}
