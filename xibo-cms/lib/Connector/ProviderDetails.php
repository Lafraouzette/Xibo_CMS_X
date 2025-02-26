<?php


namespace Xibo\Connector;

/**
 * Provider Details
 */
class ProviderDetails implements \JsonSerializable
{
    public $id;
    public $message;
    public $link;
    public $logoUrl;
    public $iconUrl;
    public $backgroundColor;
    public $mediaTypes;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'link' => $this->link,
            'logoUrl' => $this->logoUrl,
            'iconUrl' => $this->iconUrl,
            'backgroundColor' => $this->backgroundColor,
            'mediaTypes' => $this->mediaTypes
        ];
    }
}
