<?php


namespace Xibo\Widget\DataType;

use Xibo\Widget\Definition\DataType;

/**
 * Event data type
 */
class Event implements \JsonSerializable, DataTypeInterface
{
    public static $NAME = 'event';
    public $summary;
    public $description;
    public $location;

    /** @var \Carbon\Carbon */
    public $startDate;

    /** @var \Carbon\Carbon */
    public $endDate;

    /** @inheritDoc */
    public function jsonSerialize(): array
    {
        return [
            'summary' => $this->summary,
            'description' => $this->description,
            'location' => $this->location,
            'startDate' => $this->startDate->format('c'),
            'endDate' => $this->endDate->format('c'),
        ];
    }

    public function getDefinition(): DataType
    {
        $dataType = new DataType();
        $dataType->id = self::$NAME;
        $dataType->name = __('Event');
        $dataType
            ->addField('summary', __('Summary'), 'text')
            ->addField('description', __('Description'), 'text')
            ->addField('location', __('Location'), 'text')
            ->addField('startDate', __('Start Date'), 'datetime')
            ->addField('endDate', __('End Date'), 'datetime');
        return $dataType;
    }
}
