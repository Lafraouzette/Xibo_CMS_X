<?php


namespace Xibo\Widget\Definition;

/**
 * A class representing an instance of a group of elements
 * @SWG\Definition()
 */
class ElementGroup implements \JsonSerializable
{
    public $id;
    public $top;
    public $left;
    public $width;
    public $height;
    public $layer;
    public $title;
    public $slot;
    public $pinSlot;

    /** @inheritDoc */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'top' => $this->top,
            'left' => $this->left,
            'width' => $this->width,
            'height' => $this->height,
            'layer' => $this->layer,
            'title' => $this->title,
            'slot' => $this->slot,
            'pinSlot' => $this->pinSlot
        ];
    }
}
