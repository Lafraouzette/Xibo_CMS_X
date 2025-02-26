<?php


namespace Xibo\Widget\Definition;

/**
 * A class representing an instance of a group property to put a property in assigned Tab
 * @SWG\Definition()
 */
class PropertyGroup implements \JsonSerializable
{
    public $id;
    public $expanded;
    public $title;
    public $helpText;

    /** @inheritDoc */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'expanded' => $this->expanded,
            'title' => $this->title,
            'helpText' => $this->helpText
        ];
    }
}
