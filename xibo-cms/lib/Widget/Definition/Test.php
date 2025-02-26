<?php


namespace Xibo\Widget\Definition;

/**
 * Represents a test/group of conditions
 * @SWG\Definition()
 */
class Test implements \JsonSerializable
{
    /** @var string */
    public $type;

    /** @var Condition[]  */
    public $conditions;

    /** @var string|null */
    public $message;

    /** @inheritDoc */
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
            'conditions' => $this->conditions,
        ];
    }
}
