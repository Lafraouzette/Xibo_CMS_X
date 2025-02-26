<?php


namespace Xibo\Widget;

class SubPlaylistItem implements \JsonSerializable
{
    /** @var int */
    public $rowNo;

    /** @var int */
    public $playlistId;

    /** @var string */
    public $spotFill;

    /** @var int */
    public $spotLength;

    /** @var ?int */
    public $spots;

    /** @inheritDoc */
    public function jsonSerialize(): array
    {
        return [
            'rowNo' => $this->rowNo,
            'playlistId' => $this->playlistId,
            'spotFill' => $this->spotFill,
            'spotLength' => $this->spotLength,
            'spots' => $this->spots,
        ];
    }
}
