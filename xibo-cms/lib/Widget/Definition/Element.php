<?php


namespace Xibo\Widget\Definition;

/**
 * @SWG\Definition()
 * A class representing an instance of an element template
 */
class Element implements \JsonSerializable
{
    public $id;
    public $top;
    public $left;
    public $width;
    public $height;
    public $rotation;
    public $layer;
    public $elementGroupId;
    public $properties = [];

    /** @inheritDoc */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'top' => $this->top,
            'left' => $this->left,
            'width' => $this->width,
            'height' => $this->height,
            'rotation' => $this->rotation,
            'layer' => $this->layer,
            'elementGroupId' => $this->elementGroupId,
            'properties' => $this->properties
        ];
    }
}
