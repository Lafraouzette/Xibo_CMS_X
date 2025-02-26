<?php


namespace Xibo\Widget\Definition;

/**
 * A module data type
 */
class DataType implements \JsonSerializable
{
    public $id;
    public $name;

    /** @var \Xibo\Widget\Definition\Field[] */
    public $fields = [];

    public function addField(string $id, string $title, string $type, bool $isRequired = false): DataType
    {
        $field = new Field();
        $field->id = $id;
        $field->type = $type;
        $field->title = $title;
        $field->isRequired = $isRequired;
        $this->fields[] = $field;
        return $this;
    }

    /** @inheritDoc */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'fields' => $this->fields,
        ];
    }
}
