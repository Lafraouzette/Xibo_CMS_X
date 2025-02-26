<?php


namespace Xibo\Xmds\Entity;

/**
 * XMDS Depedency
 * represents a player dependency
 */
class Dependency
{
    const LEGACY_ID_OFFSET_FONT = 100000000;
    const LEGACY_ID_OFFSET_PLAYER_SOFTWARE = 200000000;
    const LEGACY_ID_OFFSET_ASSET = 300000000;
    const LEGACY_ID_OFFSET_DATA_CONNECTOR = 400000000;

    public $fileType;
    public $legacyId;
    public $id;
    public $path;
    public $size;
    public $md5;
    public $isAvailableOverHttp;

    /**
     * Prior versions of XMDS need to use a legacyId to download the file via GetFile.
     * This is a negative number in a range (to avoid collisions with existing IDs). Each dependency type should
     * resolve to a different negative number range.
     * The "real id" set on $this->id is saved in required files as the realId and used to resolve requests for this
     * type of file.
     * @param string $fileType
     * @param string|int $id
     * @param int $legacyId
     * @param string $path
     * @param int $size
     * @param string $md5
     * @param bool $isAvailableOverHttp
     */
    public function __construct(
        string $fileType,
        $id,
        int $legacyId,
        string $path,
        int $size,
        string $md5,
        bool $isAvailableOverHttp = true
    ) {
        $this->fileType = $fileType;
        $this->id = $id;
        $this->legacyId = $legacyId;
        $this->path = $path;
        $this->size = $size;
        $this->md5 = $md5;
        $this->isAvailableOverHttp = $isAvailableOverHttp;
    }
}
