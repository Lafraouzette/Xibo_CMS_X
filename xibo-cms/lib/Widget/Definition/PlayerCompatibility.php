<?php


namespace Xibo\Widget\Definition;

/**
 * Player compatibility
 */
class PlayerCompatibility implements \JsonSerializable
{
    public $windows;
    public $linux;
    public $android;
    public $webos;
    public $tizen;
    public $message;

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'windows' => $this->windows,
            'linux' => $this->linux,
            'android' => $this->android,
            'webos' => $this->webos,
            'tizen' => $this->tizen,
            'message' => $this->message,
        ];
    }
}
