<?php


namespace Xibo\Widget\Definition;

/**
 * Class representing a data type field
 */
class Field implements \JsonSerializable
{
    public $id;
    public $type;
    public $title;
    public $isRequired;

    /** @inheritDoc */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'isRequired' => $this->isRequired,
        ];
    }
}
