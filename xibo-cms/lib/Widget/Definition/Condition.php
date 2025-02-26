<?php


namespace Xibo\Widget\Definition;

/**
 * Represents a condition for a test
 */
class Condition implements \JsonSerializable
{
    public $field;
    public $type;
    public $value;

    /** @inheritDoc */
    public function jsonSerialize(): array
    {
        return [
            'field' => $this->field,
            'type' => $this->type,
            'value' => $this->value
        ];
    }
}
