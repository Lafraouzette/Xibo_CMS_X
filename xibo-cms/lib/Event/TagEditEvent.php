<?php


namespace Xibo\Event;

class TagEditEvent extends Event
{
    public static $NAME = 'tag.edit.event';
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
