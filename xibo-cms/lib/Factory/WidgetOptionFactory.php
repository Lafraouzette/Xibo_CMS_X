<?php



namespace Xibo\Factory;


use Xibo\Entity\WidgetOption;

class WidgetOptionFactory extends BaseFactory
{
    /**
     * Create Empty
     * @return WidgetOption
     */
    public function createEmpty()
    {
        return new WidgetOption($this->getStore(), $this->getLog(), $this->getDispatcher());
    }

    /**
     * Create a Widget Option
     * @param int $widgetId
     * @param string $type
     * @param string $option
     * @param mixed $value
     * @return WidgetOption
     */
    public function create($widgetId, $type, $option, $value)
    {
        $widgetOption = $this->createEmpty();
        $widgetOption->widgetId = $widgetId;
        $widgetOption->type = $type;
        $widgetOption->option = $option;
        $widgetOption->value = $value;

        return $widgetOption;
    }

    /**
     * Load by Widget Id
     * @param int $widgetId
     * @return array[WidgetOption]
     */
    public function getByWidgetId($widgetId)
    {
        return $this->query(null, array('widgetId' => $widgetId));
    }

    /**
     * Query Widget options
     * @param array $sortOrder
     * @param array $filterBy
     * @return array[WidgetOption]
     */
    public function query($sortOrder = null, $filterBy = [])
    {
        $sanitizedFilter = $this->getSanitizer($filterBy);
        $entries = [];

        $sql = 'SELECT * FROM `widgetoption` WHERE widgetId = :widgetId';

        foreach ($this->getStore()->select($sql, [
            'widgetId' => $sanitizedFilter->getInt('widgetId')
        ]) as $row) {
            $entries[] = $this->createEmpty()->hydrate($row);
        }

        return $entries;
    }
}