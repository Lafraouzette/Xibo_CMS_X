<?php


namespace Xibo\Widget\Definition;

/**
 * A Legacy Type
 * @SWG\Definition()
 */
class LegacyType implements \JsonSerializable
{
    public $name;
    public $condition;

    /** @inheritDoc */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'condition' => $this->condition,
        ];
    }
}
