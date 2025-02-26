<?php



namespace Xibo\Factory;


use Xibo\Entity\WidgetAudio;

/**
 * Class WidgetAudioFactory
 * @package Xibo\Factory
 */
class WidgetAudioFactory extends BaseFactory
{
    /**
     * Create Empty
     * @return WidgetAudio
     */
    public function createEmpty()
    {
        return new WidgetAudio($this->getStore(), $this->getLog(), $this->getDispatcher());
    }

    /**
     * Media Linked to Widgets by WidgetId
     * @param int $widgetId
     * @return array[int]
     */
    public function getByWidgetId($widgetId)
    {
        return $this->query(null, array('widgetId' => $widgetId));
    }

    /**
     * Query Media Linked to Widgets
     * @param array $sortOrder
     * @param array $filterBy
     * @return array[int]
     */
    public function query($sortOrder = null, $filterBy = [])
    {
        $entries = [];
        $sanitizedFilter = $this->getSanitizer($filterBy);

        $sql = 'SELECT `mediaId`, `widgetId`, `volume`, `loop` FROM `lkwidgetaudio` WHERE widgetId = :widgetId AND mediaId <> 0 ';

        foreach ($this->getStore()->select($sql, ['widgetId' => $sanitizedFilter->getInt('widgetId')]) as $row) {
            $entries[] = $this->createEmpty()->hydrate($row, ['intProperties' => ['duration']]);
        }

        return $entries;
    }
}