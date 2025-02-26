<?php


namespace Xibo\Event;

use Xibo\Entity\Media;

/**
 * An event fired when library media has completed upload.
 */
class LibraryUploadCompleteEvent extends Event
{
    public static $NAME = 'library.upload.complete.event';

    /** @var Media */
    protected $media;

    /**
     * @param \Xibo\Entity\Media $media
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * @return Media
     */
    public function getMedia(): Media
    {
        return $this->media;
    }
}
